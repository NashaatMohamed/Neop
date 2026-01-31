<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Exceptions\AccountNotFoundException;
use App\DataTransferObjects\TransferMoneyDto;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\SameAccountTransferException;

class TransferService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private TransactionRepository $transactionRepository
    ) {}

    public function transfer(TransferMoneyDto $dto): Transaction
    {
        // Validate amount
        if ($dto->amount <= 0) {
            return $this->recordFailedTransaction(
                fromAccountId: $dto->fromAccountId,
                toAccountId: $dto->toAccountId,
                amount: $dto->amount,
                failureReason: 'Transfer amount must be positive'
            );
        }

        if ($dto->fromAccountId === $dto->toAccountId) {
            throw new SameAccountTransferException();
        }

        try {
            return DB::transaction(function () use ($dto) {


                $fromAccount = $this->accountRepository->findByIdForUpdate($dto->fromAccountId);
                $toAccount = $this->accountRepository->findByIdForUpdate($dto->toAccountId);

                if (!$fromAccount) {
                    throw new AccountNotFoundException("Source account with ID {$dto->fromAccountId} not found");
                }

                if (!$toAccount) {
                    throw new AccountNotFoundException("Destination account with ID {$dto->toAccountId} not found");
                }

                if ($fromAccount->balance < $dto->amount) {
                    throw new InsufficientBalanceException(
                        "Insufficient balance. Available: {$fromAccount->balance}, Required: {$dto->amount}"
                    );
                }

                $fromBalanceBefore = $fromAccount->balance;
                $toBalanceBefore = $toAccount->balance;

                $this->accountRepository->updateBalance($dto->fromAccountId, -$dto->amount);
                $this->accountRepository->updateBalance($dto->toAccountId, $dto->amount);

                $fromBalanceAfter = $fromBalanceBefore - $dto->amount;
                $toBalanceAfter = $toBalanceBefore + $dto->amount;

                return $this->transactionRepository->create(array_merge($dto->toArray(), [
                    'reference_number' => $this->generateReferenceNumber(),
                    'status' => TransactionStatus::SUCCESS,
                    'from_balance_before' => $fromBalanceBefore,
                    'from_balance_after' => $fromBalanceAfter,
                    'to_balance_before' => $toBalanceBefore,
                    'to_balance_after' => $toBalanceAfter,
                    'description' => "Transfer from account #{$dto->fromAccountId} to account #{$dto->toAccountId}",
                ]));
            });
        } catch (AccountNotFoundException | InsufficientBalanceException $e) {

            $transaction = $this->recordFailedTransaction(
                fromAccountId: $dto->fromAccountId,
                toAccountId: $dto->toAccountId,
                amount: $dto->amount,
                failureReason: $e->getMessage()
            );

            throw $e;
        } catch (\Exception $e) {
            $this->recordFailedTransaction(
                fromAccountId: $dto->fromAccountId,
                toAccountId: $dto->toAccountId,
                amount: $dto->amount,
                failureReason: 'Internal error: ' . $e->getMessage()
            );

            throw $e;
        }
    }

    private function recordFailedTransaction(
        int $fromAccountId,
        int $toAccountId,
        float $amount,
        ?string $failureReason = null
    ): Transaction {
        return $this->transactionRepository->create([
            'reference_number' => $this->generateReferenceNumber(),
            'from_account_id' => $fromAccountId,
            'to_account_id' => $toAccountId,
            'amount' => $amount,
            'status' => TransactionStatus::FAILED,
            'failure_reason' => $failureReason,
            'description' => "Failed transfer from account #{$fromAccountId} to account #{$toAccountId}",
        ]);
    }


    private function generateReferenceNumber(): string
    {
        return 'TXN-' . strtoupper(Str::random(12)) . '-' . now()->format('YmdHis');
    }


    public function getAccountTransactions(int $accountId, int $perPage = 50): LengthAwarePaginator
    {
        return $this->transactionRepository->getByAccountId($accountId, $perPage);
    }

    public function getAllTransactions(int $perPage = 50): LengthAwarePaginator
    {
        return $this->transactionRepository->getAllPaginated($perPage);
    }

    public function getTransactionsBetweenAccounts(int $accountId1, int $accountId2): Collection
    {
        return $this->transactionRepository->getBetweenAccounts($accountId1, $accountId2);
    }

}

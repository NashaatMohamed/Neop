<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\TransferMoneyDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferMoneyRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function __construct(
        private TransferService $transferService
    ) {}

    public function store(TransferMoneyRequest $request): JsonResponse
    {
        $dto = TransferMoneyDto::fromArray($request->validated());

        $transaction = $this->transferService->transfer(dto:$dto);

        return response()->success(
            message: 'Transfer completed successfully',
            data: new TransactionResource($transaction)
        );
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 50);
        $accountId = $request->query('account_id');

        if ($accountId) {
            $transactions = $this->transferService->getAccountTransactions((int) $accountId, (int) $perPage);
        } else {
            $transactions = $this->transferService->getAllTransactions((int) $perPage);
        }

        return response()->paginated(
            message: 'Transactions retrieved successfully',
            paginator: $transactions->through(fn ($transaction) => new TransactionResource($transaction))
        );
    }

    public function getTransactionsBetweenAccounts(Request $request): JsonResponse
    {
        $accountId1 = $request->query('account_id_1');
        $accountId2 = $request->query('account_id_2');

        if (!$accountId1 || !$accountId2) {
            return response()->error(
                message: 'Both account_id_1 and account_id_2 are required',
                status: 400
            );
        }

        $transactions = $this->transferService->getTransactionsBetweenAccounts((int) $accountId1, (int) $accountId2);

        return response()->success(
            message: 'Transactions between accounts retrieved successfully',
            data: TransactionResource::collection($transactions)
        );
    }
}

<?php

namespace App\Services;

use App\Models\Account;
use App\Repositories\AccountRepository;
use App\DataTransferObjects\CreateAccountDto;

class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository,
    ) {}

    public function createAccount(CreateAccountDto $dto): Account
    {
        return $this->accountRepository->create($dto->toArray());
    }
}

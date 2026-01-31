<?php

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountRepository
{
    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function updateBalance(int $accountId, string $amount): bool
    {
        return Account::where('id', $accountId)
            ->update(['balance' => DB::raw("balance + ($amount)")]);
    }

    public function findByIdForUpdate(int $id): ?Account
    {
        return Account::where('id', $id)->lockForUpdate()->first();
    }

    public function getBalance(int $accountId): ?string
    {
        $account = Account::find($accountId);
        return $account?->balance;
    }
}

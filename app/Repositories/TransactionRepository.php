<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository
{

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function getByAccountId(int $accountId, int $perPage = 50): LengthAwarePaginator
    {
        return Transaction::where('from_account_id', $accountId)
            ->orWhere('to_account_id', $accountId)
            ->with(['fromAccount', 'toAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getBetweenAccounts(int $accountId1, int $accountId2): Collection
    {
        return Transaction::where(function ($query) use ($accountId1, $accountId2) {
            $query->where('from_account_id', $accountId1)
                  ->where('to_account_id', $accountId2);
        })
        ->orWhere(function ($query) use ($accountId1, $accountId2) {
            $query->where('from_account_id', $accountId2)
                  ->where('to_account_id', $accountId1);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getAllPaginated(int $perPage = 50): LengthAwarePaginator
    {
        return Transaction::with(['fromAccount', 'toAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

}

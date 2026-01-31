<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'status',
        'failure_reason',
        'reference_number',
        'from_balance_before',
        'from_balance_after',
        'to_balance_before',
        'to_balance_after',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'from_balance_before' => 'decimal:2',
        'from_balance_after' => 'decimal:2',
        'to_balance_before' => 'decimal:2',
        'to_balance_after' => 'decimal:2',
        'status' => TransactionStatus::class,
        'created_at' => 'datetime',
    ];


    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

}

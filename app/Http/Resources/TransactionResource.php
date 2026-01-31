<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->reference_number,
            'from_account_id' => $this->from_account_id,
            'to_account_id' => $this->to_account_id,
            "from_account_name" => $this->fromAccount?->name ,
            "to_account_name" => $this->toAccount?->name ,
            'amount' => $this->amount,
            'status' => $this->status,
            'failure_reason' => $this->failure_reason,
            'from_balance_before' => $this->from_balance_before,
            'from_balance_after' => $this->from_balance_after,
            'to_balance_before' => $this->to_balance_before,
            'to_balance_after' => $this->to_balance_after,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'from_account' => new AccountResource($this->whenLoaded('fromAccount')),
            'to_account' => new AccountResource($this->whenLoaded('toAccount')),
        ];
    }
}

<?php

namespace App\DataTransferObjects;

class TransferMoneyDto
{
    public function __construct(
        public readonly int $fromAccountId,
        public readonly int $toAccountId,
        public readonly float $amount,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['from_account_id'],
            $data['to_account_id'],
            $data['amount'],
        );
    }

    public function toArray(): array
    {
        return [
            'from_account_id' => $this->fromAccountId,
            'to_account_id' => $this->toAccountId,
            'amount' => $this->amount,
        ];
    }
}

<?php

namespace App\DataTransferObjects;

class CreateAccountDto
{
    public function __construct(
        public readonly int $userId,
        public readonly ?string $name = null,
        public readonly float $initialBalance = 0.00,
    ) {}


    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['name'] ?? null,
            $data['initial_balance'] ?? 0.00,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'initial_balance' => $this->initialBalance,
        ];
    }
}

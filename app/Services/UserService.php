<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }
}

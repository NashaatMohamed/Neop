<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\CreateUserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function store(CreateUserRequest $request): JsonResponse
    {
        $dto = CreateUserDto::fromArray($request->validated());

        $user = $this->userService->createUser($dto->toArray());

        return response()->success(
            message: 'User created successfully',
            data: new UserResource($user),
            status: 201
        );
    }
}

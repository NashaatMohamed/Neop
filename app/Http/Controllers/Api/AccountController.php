<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\CreateAccountDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function store(CreateAccountRequest $request): JsonResponse
    {
        $dto = CreateAccountDto::fromArray($request->validated());

        $account = $this->accountService->createAccount($dto);

        return response()->success(
            message: 'Account created successfully',
            data: new AccountResource($account),
            status: 201
        );
    }
}

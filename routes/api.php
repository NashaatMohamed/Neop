<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;









Route::post('/users', [UserController::class, 'store']);

Route::post('/accounts', [AccountController::class, 'store']);

Route::post('/transfers', [TransferController::class, 'store']);

Route::get('/transactions', [TransferController::class, 'index']);

Route::get('/transactions/between-accounts', [TransferController::class, 'getTransactionsBetweenAccounts']);

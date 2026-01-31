<?php

namespace App\Providers;

use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Services\AccountService;
use App\Services\TransferService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(AccountRepository::class);
        $this->app->singleton(TransactionRepository::class);

        // Register services
        $this->app->singleton(UserService::class);
        $this->app->singleton(AccountService::class);
        $this->app->singleton(TransferService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerResponseMacros();
    }

    /**
     * Register custom response macros.
     */
    private function registerResponseMacros(): void
    {
        Response::macro('success', function (string $message, mixed $data = null, int $status = 200): JsonResponse {
            $response = ['message' => $message];

            if ($data !== null) {
                $response['data'] = $data;
            }

            return response()->json($response, $status);
        });

        Response::macro('error', function (string $message, mixed $errors = null, int $status = 400): JsonResponse {
            $response = ['message' => $message];

            if ($errors !== null) {
                $response['error'] = $errors;
            }

            return response()->json($response, $status);
        });

        Response::macro('paginated', function (string $message, LengthAwarePaginator $paginator): JsonResponse {
            return response()->json([
                'message' => $message,
                'data' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
            ], 200);
        });
    }
}

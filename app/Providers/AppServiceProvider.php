<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\User\UserRepositoryInterface;
use App\Infrastructure\Repositories\User\EloquentUserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(
        \App\Domain\Repositories\UserRepositoryInterface::class,
        \App\Infrastructure\Repositories\EloquentUserRepository::class
        );
            $this->app->bind(
            \App\Domain\Repositories\DeputyRepositoryInterface::class,
            \App\Infrastructure\Repositories\DeputyRepository::class
        );
        $this->app->bind(
            \App\Domain\Repositories\ExpenseRepositoryInterface::class,
            \App\Infrastructure\Repositories\ExpenseRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Ports\UserRepositoryInterface;
use App\Domain\Ports\EmailServiceInterface;
use App\Domain\Ports\TokenServiceInterface;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Infrastructure\Services\LaravelEmailService;
use App\Infrastructure\Services\LaravelTokenService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registro de dependencias de la arquitectura hexagonal
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(EmailServiceInterface::class, LaravelEmailService::class);
        $this->app->bind(TokenServiceInterface::class, LaravelTokenService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

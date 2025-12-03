<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Ports\UserRepositoryInterface;
use App\Domain\Ports\EmailServiceInterface;
use App\Domain\Ports\TokenServiceInterface;
use App\Domain\Ports\DailyQuoteRepositoryInterface;
use App\Domain\Ports\AIServiceInterface;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Infrastructure\Persistence\EloquentDailyQuoteRepository;
use App\Infrastructure\Services\LaravelEmailService;
use App\Infrastructure\Services\LaravelTokenService;
use App\Infrastructure\Services\AIService;

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
        $this->app->bind(DailyQuoteRepositoryInterface::class, EloquentDailyQuoteRepository::class);
        $this->app->bind(AIServiceInterface::class, AIService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

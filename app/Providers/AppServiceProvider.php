<?php

namespace App\Providers;

use App\Services\AI\AIClientInterface;
use App\Services\AI\FakeAIClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AIClientInterface::class,
            FakeAIClient::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

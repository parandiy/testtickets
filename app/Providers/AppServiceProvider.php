<?php

namespace App\Providers;

use App\Services\AI\AIClientInterface;
use App\Services\AI\FakeAIClient;
use App\Services\AI\OpenAIClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('testing')) {
            $this->app->bind(AIClientInterface::class, FakeAIClient::class);
        } else {
            $this->app->bind(AIClientInterface::class, OpenAIClient::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq;

use dkhorev\LaravelAmpq\Commands\ListenAmpqTopicCommand;
use dkhorev\LaravelAmpq\Contracts\ReceiveMessageServiceContract;
use dkhorev\LaravelAmpq\Contracts\SendToTopicServiceContract;
use dkhorev\LaravelAmpq\Services\ReceiveMessageService;
use dkhorev\LaravelAmpq\Services\SendToTopicService;
use Illuminate\Support\ServiceProvider;

class LaravelAmpqServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            ListenAmpqTopicCommand::class,
        ]);

        $this->publishes(
            [
                __DIR__ . '/laravel-ampq.php' => config_path('laravel-ampq.php'),
            ],
            'laravel-ampq'
        );
    }

    public function register()
    {
        $this->app->bind(SendToTopicServiceContract::class, SendToTopicService::class);
        $this->app->bind(ReceiveMessageServiceContract::class, ReceiveMessageService::class);
    }
}

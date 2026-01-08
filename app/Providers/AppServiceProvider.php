<?php

namespace App\Providers;

use App\Commands\Telegram\StartCommand;
use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Laravel\Facades\Telegram;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!defined('L5_SWAGGER_CONST_HOST')) {
            define('L5_SWAGGER_CONST_HOST', env('L5_SWAGGER_CONST_HOST', config('app.url', 'http://localhost')));
        }

        Telegram::addCommands([
            StartCommand::class,
        ]);
    }
}

<?php


namespace CloudMyn\Logger;

use CloudMyn\Logger\Utils\Logger;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    /**
     *  Call when app everything in application is ready
     *  including third-party libraries
     */
    public function boot()
    {
        // load routes
        $this->registerRoutes();

        // load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cloudmyn_logger');

        // publish configuration and migration
        // cmd: php artisan vendor:publish --provider="CloudMyn\Logger\LoggerServiceProvider" --tag="config"
        if ($this->app->runningInConsole()) {

            // publish config file
            $this->publishes([
                __DIR__ . '/../config/logger.php' => config_path('logger.php'),
            ], 'config');

            // ...
        }
    }

    /**
     *  Call before anything setup
     */
    public function register()
    {
        // register helper functions
        require_once __DIR__ . "/Helpers/helper_functions.php";

        // register facade
        $this->app->bind('logger', function ($app) {
            return new Logger();
        });
    }

    protected function registerRoutes(): void
    {
        if(config('logger.enable_route', false) === true) {
            Route::group($this->routeConfiguration(), function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        }
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('logger.prefix', ""),
            'middleware' => config('logger.middleware', []),
        ];
    }
}

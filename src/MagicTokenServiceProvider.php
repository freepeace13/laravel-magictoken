<?php

namespace MagicToken;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MagicTokenServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/magictoken.php', 'magictoken');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();

        $this->bootViews();
        $this->bootRoutes();
        $this->bootRequestMacros();
    }

    protected function bootViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'magictoken');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/magictoken')
        ]);
    }

    protected function bootRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'prefix' => config('magictoken.http.path')
        ], __DIR__.'/routes.php');
    }

    protected function bootRequestMacros()
    {
        Request::macro('hasValidToken', function() {
            return ! is_null(TokenRepository::findPendingToken(
                $this->tokenInput()
            ));
        });

        Request::macro('tokenInput', function () {
            return $this->route(
                config('magictoken.http.input_keys.token'),
                null
            );
        });

        Request::macro('pincodeInput', function () {
            return $this->input(
                config('magictoken.http.input_keys.pincode'),
                null
            );
        });
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/magictoken.php' => config_path('magictoken.php'),
        ], 'config');
    }

    protected function publishMigrations()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/0000_00_00_000000_create_magic_tokens_table.php' => database_path('/migrations/0000_00_00_000000_create_magic_tokens_table.php'),
        ], 'migrations');
    }
}

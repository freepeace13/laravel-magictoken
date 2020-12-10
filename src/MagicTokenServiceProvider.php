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
    }

    protected function bootViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'magictoken');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/magictoken')
        ]);
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

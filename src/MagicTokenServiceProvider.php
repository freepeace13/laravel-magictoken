<?php

namespace MagicToken;

use Illuminate\Support\Str;
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

        $this->app->singleton('magictoken', function ($app) {
            return new MagicToken($app['magictoken.repository']);
        });

        $this->app->bind('magictoken.repository', function ($app) {
            $key = $app['config']['app.key'];

            if (Str::startsWith($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            return new MagicTokenRepository(
                DatabaseToken::class,
                $key,
                $app['config']['magictoken.length'],
                $app['config']['magictoken.max_tries'],
                $app['config']['magictoken.database.expires']
            );
        });
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

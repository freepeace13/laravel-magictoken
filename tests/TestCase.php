<?php

namespace MagicToken\Tests;

use MagicToken\MagicToken;
use MagicToken\MagicTokenServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Set the package service provider.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [MagicTokenServiceProvider::class];
    }

     /**
      * Define environment setup.
      *
      * @param  \Illuminate\Foundation\Application  $app
      * @return void
      */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('session.driver', 'array');

        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

     /**
      * Setup the test environment.
      *
      * @return void
      */
    protected function setUp(): void
    {
        parent::setUp();

        MagicToken::routes();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}

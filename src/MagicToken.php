<?php

namespace MagicToken;

use Illuminate\Support\Facades\Route;
use MagicToken\Middlewares\ValidateMagicToken;

class MagicToken
{
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => config('magictoken.path'),
            'middleware' => ValidateMagicToken::class
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}

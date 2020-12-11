<?php

namespace MagicToken;

use DateTimeInterface;
use Illuminate\Support\Facades\Route;
use MagicToken\Middlewares\ValidateMagicToken;
use Illuminate\Support\Traits\ForwardsCalls;

class MagicToken
{
    use ForwardsCalls;

    protected $tokens;

    public function __construct(MagicTokenRepository $tokens)
    {
        $this->tokens = $tokens;
    }

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

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->tokens, $method, $parameters);
    }
}

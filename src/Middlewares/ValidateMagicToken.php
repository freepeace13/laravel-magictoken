<?php

namespace MagicToken\Middlewares;

use Closure;
use MagicToken\Facades\MagicToken;
use Illuminate\Http\Request;
use MagicToken\DatabaseMagicToken;

class ValidateMagicToken
{
    public function handle(Request $request, Closure $next)
    {
        $tokenQuery = $request->query('token');

        $instance = DatabaseMagicToken::findPendingToken($tokenQuery);

        if (is_null($instance)) {
            abort(401);
        }

        return $next($request);
    }
}

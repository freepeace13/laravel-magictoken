<?php

namespace MagicToken\Middlewares;

use Closure;
use Illuminate\Http\Request;

class ValidateToken
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasValidToken()) {
            abort(401);
        }

        return $next($request);
    }
}

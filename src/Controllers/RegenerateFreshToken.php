<?php

namespace MagicToken\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;
use MagicToken\TokenRepository;

class RegenerateFreshToken
{
    /**
     * Handle incoming request.
     *
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $token = TokenRepository::replicate($request->tokenInput());

        if (! $request->wantsJson()) {
            return Redirect::to($token->url);
        }

        return Response::json([
            'verify_url' => $token->url
        ], 200);
    }
}

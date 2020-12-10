<?php

namespace MagicToken\Controllers;

use Illuminate\Http\Request;
use MagicToken\JsonResponse;
use MagicToken\DatabaseMagicToken;
use Illuminate\Support\Facades\Redirect;

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
        $tokenQuery = $request->query('token');

        $original = DatabaseMagicToken::findPendingToken($tokenQuery);

        $newValue = DatabaseMagicToken::createFrom($original);

        if ($request->wantsJson()) {
            return (new JsonResponse)($newValue->token);
        }

        return Redirect::route('magictoken.verify', [
            'token' => $newValue->token
        ]);
    }
}

<?php

namespace MagicToken\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use MagicToken\JsonResponse;
use Illuminate\Routing\Controller;

class AccessTokensForm extends Controller
{
    /**
     * Handle incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $token = $request->query('token');

        if ($request->wantsJson()) {
            return (new JsonResponse)($token);
        }

        return View::make(config('magictoken.view'));
    }
}

<?php

namespace MagicToken\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AccessTokensForm
{
    /**
     * Handle incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        if ($request->wantsJson()) {
            return Response::noContent();
        }

        return view('magictoken::verify');
    }
}

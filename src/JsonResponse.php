<?php

namespace MagicToken;

use Illuminate\Support\Facades\Response;

class JsonResponse
{
    public function __invoke(string $token)
    {
        return Response::json([
            'token' => $token,
            'links' => [
                'verify' => route('magictoken.verify'),
                'resend' => route('magictoken.resend')
            ]
        ]);
    }
}

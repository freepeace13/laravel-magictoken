<?php

namespace MagicToken;

use MagicToken\Controllers\AccessTokensForm;
use MagicToken\Controllers\VerifyTokensPincode;
use MagicToken\Controllers\RegenerateFreshToken;
use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function all()
    {
        $this->forAccessToken();
        $this->forVerifyToken();
        $this->forResendToken();
    }

    public function forAccessToken()
    {
        $this->router->get('/verify', [
            'uses' => AccessTokensForm::class,
            'as' => 'magictoken.verify'
        ]);
    }

    public function forVerifyToken()
    {
        $this->router->post('/verify', [
            'uses' => VerifyTokensPincode::class,
            'as' => 'magictoken.verify'
        ]);
    }

    public function forResendToken()
    {
        $this->router->post('/resend', [
            'uses' => RegenerateFreshToken::class,
            'as' => 'magictoken.resend'
        ]);
    }
}

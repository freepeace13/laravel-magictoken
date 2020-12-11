<?php

namespace MaginToken\Test\Dummy;

use Illuminate\Support\Str;
use MagicToken\Contracts\Action;

class GenerateRandomString implements Action
{
    public $token;

    public function __construct(int $length = 64)
    {
        $this->token = Str::random($length);
    }

    public function handle()
    {
        return $this->token;
    }
}

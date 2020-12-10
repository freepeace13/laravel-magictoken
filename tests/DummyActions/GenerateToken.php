<?php

namespace MaginToken\Test\DummyActions;

use Illuminate\Support\Str;
use MagicToken\Contracts\Action;

class GenerateToken implements Action
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

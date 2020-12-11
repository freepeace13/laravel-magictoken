<?php

namespace MagicToken\Tests\Dummy;

use Illuminate\Support\Str;
use MagicToken\Action;

class GenerateRandomString implements Action
{
    public $value;

    public function __construct()
    {
        $this->value = Str::random(40);
    }

    public function handle()
    {
        return $this->value;
    }
}

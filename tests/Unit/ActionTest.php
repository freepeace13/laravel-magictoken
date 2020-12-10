<?php

namespace MagicToken\Test\Unit;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use MagicToken\Contracts\Action;

class ActionTest extends TestCase
{
    public function test_action_correct_output()
    {
        $action = new GenerateRandomToken(64);

        $this->assertTrue(strlen($action->token) === 64);

        $this->assertEquals($action->token, $action->handle());
    }
}


class GenerateRandomToken implements Action
{
    public $token;

    public function __construct(int $length)
    {
        $this->token = Str::random($length);
    }

    public function handle()
    {
        return $this->token;
    }
}

<?php

namespace MaginToken\Tests\DummyActions;

use MaginToken\MagicToken;

class PrintMobileNumber implements ActionInterface
{
    public $mobile;

    public function __construct($mobile)
    {
        $this->mobile = $mobile;
    }

    public function handle()
    {
        return $this->mobile;
    }
}

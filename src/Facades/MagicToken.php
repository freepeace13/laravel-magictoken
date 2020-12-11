<?php

namespace MagicToken\Facades;

use Illuminate\Support\Facades\Facade;

class MagicToken extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
     protected static function getFacadeAccessor()
     {
         return 'magictoken';
     }
}

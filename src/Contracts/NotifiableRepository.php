<?php

namespace MagicToken\Contracts;

use MagicToken\MagicToken;

interface NotifiableRepository
{
    public static function findByToken(MagicToken $token);

    public static function findByEmail(string $email);

    public static function findByMobile(string $mobile);
}

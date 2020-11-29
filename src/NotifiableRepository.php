<?php

namespace MagicToken;

use MagicToken\Contracts\NotifiableRepository as RepositoryContract;

class NotifiableRepository implements RepositoryContract
{
    public static function findByToken(MagicToken $token) {
        if (property_exists($token->action, 'mobile')) {
            return self::findByMobile($token->action->mobile);
        }

        if (property_exists($token->action, 'email')) {
            return self::findByEmail($token->action->email);
        }

        return null;
    }

    public static function findByMobile(string $email) {}

    public static function findByMobile(string $mobile) {}
}

<?php

namespace MagicToken;

use Illuminate\Http\Request;
use MagicToken\Exceptions\InvalidTokenException;

class TokenRepository
{
    /**
     * Replicate action and other options from existing token and apply newly created token.
     *
     * @param mixed $token
     * @return MagicToken\MagicToken
     */
    public static function replicate($token)
    {
        $original = self::findValidToken($token);

        $expires = $original->created_at->diffInMinutes($original->expires_at);

        $newValue = MagicToken::create(
            $original->action,
            $expires,
            $original->maxTries
        );

        $original->delete();

        return $newValue;
    }

    /**
     * Find pending token that throws an exception when not found.
     *
     * @param mixed $token
     * @return self
     *
     * @throws MagicToken\Exceptions\InvalidTokenException
     */
     public static function findValidToken($token)
     {
         $existing = self::findPendingToken($token);

         if (is_null($existing)) {
             throw new InvalidTokenException('Token expired or deleted.');
         }

         return $existing;
     }

    /**
     * Find unexpired, unverified and retriable token record.
     *
     * @param mixed $token
     * @return self
     */
     public static function findPendingToken($token)
     {
         return self::expired(false)
             ->retriable()
             ->whereToken($token)
             ->first();
     }

    /**
     * Delete all existing expired tokens.
     *
     * @return bool
     */
     public static function deleteExpiredTokens()
     {
         return self::expired(true)->delete();
     }
}

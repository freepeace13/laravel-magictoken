<?php

namespace MagicToken;

use Illuminate\Validation\ValidationException;

class VerifyToken
{
    /**
     * Replicate action and other options from existing token and apply newly created token.
     *
     * @param mixed $token
     * @return MagicToken\MagicToken
     */
    public static function resend($token)
    {
        $original = MagicToken::findValidToken($token);

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
     * Verify the given token and pincode.
     *
     * Increments num_tries on fail and mark token as verified on success.
     *
     * @param mixed $token
     * @param mixed $pincode
     * @return MagicToken\MagicToken
     */
    public static function attempt($token, $pincode)
    {
        $existing = MagicToken::findValidToken($token);

        if ((string) $existing->code !== (string) $pincode) {
            $existing->increment('num_tries');

            throw ValidationException::withMessage([
                config('magictoken.http_requests.form_inputs.pincode') => 'Incorrect pincode.'
            ]);
        }

        return $existing->markVerified();
    }
}

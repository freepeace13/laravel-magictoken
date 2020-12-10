<?php

namespace MagicToken\Controllers;

use Illuminate\Http\Request;
use MagicToken\DatabaseMagicToken;
use Illuminate\Validation\ValidationException;

class VerifyTokensPincode
{
    public function __invoke(Request $request)
    {
        $tokenQuery = $request->query('token');

        $existing = DatabaseMagicToken::findPendingToken($tokenQuery);

        if ($this->attemptVerify($existing, $request->input('pincode'))) {
            $result = $existing->action->handle();

            $existing->delete();

            return $result;
        }
    }

    protected function attemptVerify(DatabaseMagicToken $token, $pincode)
    {
        if ((string) $token->code !== (string) $pincode) {
            $token->increment('num_tries');

            $this->throwIncorrectPincodeErrors();
        }

        return true;
    }

    protected function throwIncorrectPincodeErrors()
    {
        throw ValidationException::withMessage([
            'pincode' => $this->incorrectPincodeMessage()
        ]);
    }

    protected function incorrectPincodeMessage()
    {
        return 'Incorrect pincode.';
    }
}

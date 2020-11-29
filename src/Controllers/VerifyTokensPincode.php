<?php

namespace MagicToken\Http\Controllers;

use Exception;
use MagicToken\MagicToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MagicToken\TokenRepository;

class VerifyTokensPincode
{
    public function __invoke(Request $request)
    {
        $existing = TokenRepository::findValidToken(
            $request->tokenInput()
        );

        if ($this->attemptVerify($existing, $request->pincodeInput())) {
            $result = $existing->action->handle();

            $existing->delete();

            return $result;
        }
    }

    protected function attemptVerify(MagicToken $token, $pincode)
    {
        if ((string) $token->code !== (string) $pincode) {
            $token->increment('num_tries');

            $this->throwIncorrectPincodeErrors();
        }

        return true;
    }

    protected function throwIncorrectPincodeErrors()
    {
        $inputKey = config('magictoken.http.input_keys.pincode');

        throw ValidationException::withMessage([
            $inputKey => $this->incorrectPincodeMessage()
        ]);
    }

    protected function incorrectPincodeMessage()
    {
        return 'Incorrect pincode.';
    }
}

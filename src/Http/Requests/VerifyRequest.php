<?php

namespace MagicToken\Http;

use Illuminate\Validation\Rule;
use MagicToken\Http\FormRequest;

class VerifyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            config('magictoken.http_requests.form_inputs.pincode') => [
                'required',
                'size:' . config('magictoken.code_length'),
            ],
        ]);
    }
}

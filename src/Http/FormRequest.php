<?php

namespace MagicToken\Http;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            config('magictoken.http_requests.form_inputs.token') => [
                'required',
                Rule::exists(config('magictoken.database.table_name'), 'token')
                    ->where(function ($query) {
                        $query
                            ->whereNull('verified_at')
                            ->expired(false)
                            ->retriable();
                    }),
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return array_flip(config('magictoken.http_requests.form_inputs'));
    }
}

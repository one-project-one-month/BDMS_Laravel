<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class BaseFormRequest extends FormRequest
{
    public function attributes(): array
    {
        $attributes = [];

        foreach (array_keys($this->rules()) as $key) {
            $attributes[$key] = Str::camel($key);
        }

        return $attributes;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = [];
        foreach ($validator->errors()->toArray() as $key => $messages) {
            $camelKey = Str::camel($key);
            $errors[$camelKey] = $messages;
        }

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'The given data was invalid.',
            'errors' => $errors
        ], 422));
    }
}

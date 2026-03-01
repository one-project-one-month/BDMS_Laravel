<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id') ? $this->route('id')->id : null;

        return [
            'hospitalId' => 'required|exists:hospitals,id',
            'roleId' => 'required|exists:roles,id',
            'userName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => $userId ? 'nullable|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/' : 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/',
            'is_active' => 'boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}


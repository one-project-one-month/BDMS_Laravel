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
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'hospital_id' => 'required|exists:hospitals,id',
            'user_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone' . $userId,
            'password' => $userId ? 'nullable|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/' : 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/',
            'role' => 'required|in:admin,staff,user',
            'is_active' => 'boolean'
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


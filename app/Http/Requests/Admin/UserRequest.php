<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [
            'hospital_id' => $this->hospitalId,
            'role_id' => $this->roleId,
            'user_name' => $this->userName,
            'is_active' => $this->has('isActive')
                ? filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN)
                : true,
        ];

        $this->merge($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'hospital_id' => 'nullable|exists:hospitals,id',
            'role_id' => 'required|exists:roles,id',
            'user_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $userId ? 'nullable|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/' : 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/',
            'is_active' => 'boolean',
        ];
    }
}

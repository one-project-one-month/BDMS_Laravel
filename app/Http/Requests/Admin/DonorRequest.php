<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DonorRequest extends FormRequest
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
        $donorId = $this->route('donor') ? $this->route('donor')->id : null;

        return [
            'user_id' => 'required|exists:users,id|unique:donors,user_id,' . $donorId,
            'nrc_no' => 'required|string|unique:donors,nrc_no,' . $donorId,
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'emergency_contact' => 'required|string',
            'emergency_phone' => 'required|string',
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

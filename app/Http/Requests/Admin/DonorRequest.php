<?php

namespace App\Http\Requests\Admin;

use App\Enums\Gender;
use App\Enums\BloodGroup;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [
            'user_id'            => $this->userId,
            'nrc_no'             => $this->nrcNo,
            'date_of_birth'      => $this->dateOfBirth,
            'blood_group'        => $this->bloodGroup,
            'emergency_contact'  => $this->emergencyContact,
            'emergency_phone'    => $this->emergencyPhone,
            'is_active' => $this->has('isActive')
                ? filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN)
                : true,
            'last_donation_date' => $this->lastDonationDate ?? null,
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
        // $donorId = $this->route('id') ? $this->route('id')->id : null;
        $donorId = $this->route('donor');

        return [
            'user_id'             => [
                'required',
                'exists:users,id',
                Rule::unique('donors', 'user_id')->ignore($donorId),
            ],

            'nrc_no'              => [
                'required',
                'string',
                Rule::unique('donors', 'nrc_no')->ignore($donorId),
            ],
            'date_of_birth'       => 'required|date',
            'gender'              => 'required|in:' . implode(',', Gender::values()),
            'blood_group'         => 'required|in:' . implode(',', BloodGroup::values()),
            'weight'              => 'required|numeric|min:1',
            'last_donation_date'  => 'nullable|date',
            'remarks'             => 'nullable|string',
            'emergency_contact'   => 'required|string|max:255',
            'emergency_phone'     => 'required|string|max:20',
            'address'             => 'required|string',
            'is_active'           => 'boolean',
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

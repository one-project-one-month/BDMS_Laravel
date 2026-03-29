<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use App\Enums\BloodGroup;
use App\Enums\Gender;
use Illuminate\Validation\Rules\Enum;

class ProfileDonorRequest extends BaseFormRequest
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
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nrc_no' => $this->nrcNo,
            'date_of_birth' => $this->dateOfBirth,
            'blood_group' => $this->bloodGroup,
            'last_donation_date' => $this->lastDonationDate?->format('Y-m-d'),
            'emergency_contact' => $this->emergencyContact,
            'emergency_phone' => $this->emergencyPhone,
            'is_active' => $this->isActive,
        ]);
    }

    public function rules(): array
    {
        return [
            'nrc_no' => 'required|string|max:50|unique:donors,nrc_no',
            'date_of_birth' => 'required|date|before:today',
            'gender' => ['required', new Enum(Gender::class)],
            'blood_group' => ['required', new Enum(BloodGroup::class)],
            'weight' => 'required|numeric|between:30,999.99',
            'last_donation_date' => 'nullable|date|before_or_equal:today',
            'remarks' => 'nullable|string|max:1000',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'is_active' => 'boolean',
        ];
    }
}

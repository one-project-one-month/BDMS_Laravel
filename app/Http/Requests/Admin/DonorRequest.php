<?php

namespace App\Http\Requests\Admin;

use App\Enums\Gender;
use App\Enums\BloodGroup;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class DonorRequest extends BaseFormRequest
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
        $this->merge([
            'user_id' => $this->userId,
            'nrc_no' => $this->nrcNo,
            'date_of_birth' => $this->dateOfBirth,
            'blood_group' => $this->bloodGroup,
            'emergency_contact' => $this->emergencyContact,
            'emergency_phone' => $this->emergencyPhone,
            'last_donation_date' => $this->lastDonationDate ?? null,
            'is_active' => $this->has('isActive')
                ? filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN)
                : true,
            'gender' => $this->gender,
            'weight' => $this->weight,
            'address' => $this->address,
            'remarks' => $this->remarks,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $donorId = $this->route('donor');

        return [
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('donors', 'user_id')->ignore($donorId),
            ],

            'nrc_no' => [
                'required',
                'string',
                'regex:/^[0-9]{1,2}\/[A-Z]+\([A-Z]\)[0-9]{6}$/',
                Rule::unique('donors', 'nrc_no')->ignore($donorId),
            ],

            'date_of_birth' => [
                'required',
                'date',
                'before:' . now()->subYears(18)->format('Y-m-d'),
            ],

            'gender' => [
                'required',
                Rule::in(Gender::values()),
            ],

            'blood_group' => [
                'required',
                Rule::in(BloodGroup::values()),
            ],

            'weight' => [
                'required',
                'numeric',
                'min:45',
                'max:250',
            ],

            'last_donation_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'emergency_contact' => [
                'required',
                'string',
                'max:255',
            ],

            'emergency_phone' => [
                'required',
                'string',
                'regex:/^(09|\+?959)[0-9]{7,9}$/',
            ],

            'address' => [
                'required',
                'string',
                'max:500',
            ],

            'is_active' => [
                'boolean',
            ],
        ];
    }
}

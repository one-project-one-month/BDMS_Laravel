<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\BloodGroup;
use App\Enums\Gender;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileDonorRequest extends FormRequest
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
            'nrc_no'             => $this->nrcNo,
            'date_of_birth'      => $this->dateOfBirth,
            'blood_group'        => $this->bloodGroup,
            'last_donation_date' => $this->lastDonationDate,
            'emergency_contact'  => $this->emergencyContact,
            'emergency_phone'    => $this->emergencyPhone,
            'is_active'          => $this->isActive,
        ]);
    }

    public function rules(): array
    {
        return [
            'nrcNo'              => 'required|string|max:50|unique:donors,nrc_no',
            'dateOfBirth'        => 'required|date|before:today',
            'gender'             => ['required', new Enum(Gender::class)],
            'bloodGroup'         => ['required', new Enum(BloodGroup::class)],
            'weight'             => 'required|numeric|between:30,999.99',
            'lastDonationDate'   => 'nullable|date|before_or_equal:today',
            'remarks'            => 'nullable|string|max:1000',
            'emergencyContact'   => 'required|string|max:255',
            'emergencyPhone'     => 'required|string|max:20',
            'address'            => 'required|string',
            'isActive'           => 'boolean',
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

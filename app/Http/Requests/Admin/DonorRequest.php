<?php

namespace App\Http\Requests\Admin;

use App\Enums\Gender;
use App\Enums\BloodGroup;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;


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
        return [
            'hospital_id' => 'required|exists:hospitals,id',
            'blood_group' => 'required|in:' . implode(',',BloodGroup::values()),
            'units_donated' => 'required|integer|min:1|max:5',
            'donation_date' => 'required|date',
            'remarks'       => 'nummable|string',
        ];
           
            
    }


        
}

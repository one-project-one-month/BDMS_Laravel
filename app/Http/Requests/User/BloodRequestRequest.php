<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class BloodRequestRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'hospital_id' => $this->hospitalId,
            'patient_name' => $this->patientName,
            'blood_group' => $this->bloodGroup,
            'units_required' => $this->unitsRequired,
            'contact_phone' => $this->contactPhone,
            'urgency' => $this->urgency,
            'required_date' => $this->requiredDate,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //for cancel a blood request
        if ($this->isMethod('patch')) {
            return
                [
                    'status' => ['required', 'in:cancelled']
                ];
        }


        return
            [
                'hospital_id' => ['required', 'exists:hospitals,id'],
                'patient_name' => ['required', 'string'],
                'blood_group' => ['required', Rule::in(\App\Enums\BloodGroup::values())],
                'units_required' => ['required', 'integer'],
                'contact_phone' => ['required', 'string'],
                'urgency' => ['required', Rule::in(\App\Enums\Urgency::values())],
                'required_date' => ['required', 'date_format:d-m-Y', 'after_or_equal:today'],
                'reason' => ['required', 'string']
            ];
    }
}

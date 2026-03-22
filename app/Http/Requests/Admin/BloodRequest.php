<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\BloodGroup;
use App\Enums\Urgency;

class BloodRequest extends BaseFormRequest
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
            'user_id' => $this->userId,
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
        return [
            'user_id' => 'required|exists:users,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'patient_name' => 'required|string|max:255',
            'blood_group' => ['required', new Enum(BloodGroup::class)],
            'units_required' => 'required|integer|min:1',
            'contact_phone' => 'required|string|max:20',
            'urgency' => ['required', new Enum(Urgency::class)],
            'required_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string',
        ];
    }
}

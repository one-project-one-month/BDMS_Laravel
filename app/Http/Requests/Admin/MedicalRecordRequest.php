<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation()
    {
        $this->merge([
            'donation_id' => $this->donationId,
            'hospital_id' => $this->hospitalId,
            'hemoglobin_level' => $this->hemoglobinLevel,
            'hiv_result' => $this->hivResult,
            'hepatitis_b_result' => $this->hepatitisBResult,
            'hepatitis_c_result' => $this->hepatitisCResult,
            'malaria_result' => $this->malariaResult,
            'syphilis_result' => $this->syphilisResult,
            'blood_group' => $this->bloodGroup,
            'screening_status' => $this->screeningStatus,
            'screening_notes' => $this->screeningNotes,
            'screening_by' => $this->screeningBy,
            'screening_at' => $this->screeningAt,
        ]);
    }

    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        return [
            'donation_id' => ['required', 'exists:donations,id'],
            'hospital_id' => ['required', 'exists:hospitals,id'],
            'hemoglobin_level' => ['nullable', 'numeric'],
            'hiv_result' => ['nullable', 'string'],
            'hepatitis_b_result' => ['nullable', 'string'],
            'hepatitis_c_result' => ['nullable', 'string'],
            'malaria_result' => ['nullable', 'string'],
            'syphilis_result' => ['nullable', 'string'],
            'blood_group' => ['required', 'string'],
            'screening_status' => ['required', 'string'],
            'screening_notes' => ['nullable', 'string'],
            'screening_by' => ['nullable', 'exists:users,id'],
            'screening_at' => ['nullable', 'date']
        ];
    }

    /**
     * Custom API Error Response
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}

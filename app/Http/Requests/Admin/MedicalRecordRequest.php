<?php

namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class MedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donation_id' => ['required', 'exists:donations,id'],
            'hospital_id' => ['required','exists:hospitals,id'],
            'homeglobin_level' => ['nullable','numeric'],
            'hiv_result' => ['nullable','string'],
            'hepatitis_b_result' => ['nullable','string'],
            'hepatitis_c_result' => ['nullable','string'],
            'malaria_result' => ['nullable','string'],
            'syphilis_result' => ['nullable','string'],
            'blood_group' =>['required','string'],
            'screening_status' => ['requried','string'],
            'screening_notes' => ['nullable','string'],
            'creeening_by' => ['nullable','exists:users,id'],
            'screening_at' => ['nullable','date']
        ];
    }
}
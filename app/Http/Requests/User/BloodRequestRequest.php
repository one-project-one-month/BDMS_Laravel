<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BloodRequestRequest extends FormRequest
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
        //for cancel a blood request
        if($this->isMethod('patch'))
        {
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
            'required_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string']
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

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;
use App\Enums\BloodGroup;
use App\Enums\Urgency;

class BloodRequest extends FormRequest
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}

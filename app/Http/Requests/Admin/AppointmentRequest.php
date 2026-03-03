<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
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
            'appointmentDate' => $this->appointmentDate,
            'appointmentTime' => $this->appointmentTime,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //for toggle status
        if ($this->isMethod('patch')) {
            return [
                'status' => [
                    'required',
                    Rule::in(\App\Enums\AppointmentStatus::values())
                ],
            ];
        }

        return [
            'appointmentDate' => 'required|date',
            'appointmentTime' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled, cancelled, confirmed, completed',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->userId,
            'hospital_id' => $this->hospitalId,
            'donation_id' => $this->donationId,
            'blood_request_id' => $this->bloodRequestId,
            'appointment_date' => $this->appointmentDate,
            'appointment_time' => $this->appointmentTime,
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}

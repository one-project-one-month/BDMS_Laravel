<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'user_id' => 'required|exists:users,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'donation_id' => 'nullable|exists:donations,id|required_without:blood_request_id',
            'blood_request_id' => 'nullable|exists:blood_requests,id|required_without:donation_id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled, cancelled, confirmed, completed',
            'remarks' => 'nullable|string|max:255',
        ];
    }
}

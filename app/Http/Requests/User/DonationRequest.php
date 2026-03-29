<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class DonationRequest extends BaseFormRequest
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
            'blood_request_id' => $this->bloodRequestId,
            'donation_date' => $this->donationDate,
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
            'hospital_id' => 'required|exists:hospitals,id',
            'blood_request_id' => 'nullable|exists:blood_requests,id',
            'donation_date' => 'required|date|after_or_equal:today',
            'remarks' => 'nullable|string',
        ];
    }
}

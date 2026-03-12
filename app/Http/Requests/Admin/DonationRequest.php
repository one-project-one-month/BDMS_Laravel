<?php

namespace App\Http\Requests\Admin;

use App\Enums\BloodGroup;
use App\Enums\DonationStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'donor_id' => $this->donorId,
            'hospital_id' => $this->hospitalId,
            'blood_request_id' => $this->bloodRequestId,
            'created_by' => $this->createdBy,
            'blood_group' => $this->bloodGroup,
            'units_donated' => $this->unitsDonated,
            'donation_date' => $this->donationDate,
            'approved_by' => $this->approvedBy,
            'approved_at' => $this->approvedAt,
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
            'donor_id' => 'required|exists:donors,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'blood_request_id' => 'nullable|exists:blood_requests,id',
            'created_by' => 'required|exists:users,id',
            'blood_group' => ['required', 'string', Rule::in(BloodGroup::values())],
            'units_donated' => 'required|integer|min:1',
            'donation_date' => 'required|date',
            'status' => ['required', 'string', Rule::in(DonationStatus::values())],
            'approved_by' => 'nullable|exists:users,id',
            'approved_at' => 'nullable|date',
            'remarks' => 'nullable|string',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
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

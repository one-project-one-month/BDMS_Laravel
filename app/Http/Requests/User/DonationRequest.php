<?php

namespace App\Http\Requests\User;

use App\Enums\BloodGroup;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;

class DonationRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $donor = $user->donor;

        if (!$donor)
            return false;

        if ($donor->last_donation_date) {
            return now()->diffInDays($donor->last_donation_date) >= 90;
        }

        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'donor_id' => auth()->user()->donor?->id,
            'hospital_id' => $this->hospitalId,
            'blood_request_id' => $this->bloodRequestId,
            'blood_group' => $this->bloodGroup,
            'units_donated' => $this->unitsDonated,
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
            'donor_id' => 'required|exists:donors,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'blood_request_id' => 'nullable|exists:blood_requests,id',
            'blood_group' => ['required', new Enum(BloodGroup::class)],
            'units_donated' => 'required|integer|min:1',
            'donation_date' => 'required|date|before_or_equal:today',
            'remarks' => 'nullable|string',
        ];
    }
}

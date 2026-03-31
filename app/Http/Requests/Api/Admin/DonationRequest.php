<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\BloodGroup;

class DonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation():void
    {
        $this->merge([
            'hospital_id' => $this->hospitalId,
            'blood_group' => $this->bloodGroup,
            'units_donated' =>$this->unitsDonated,
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
            //
            
            'hospital_id' => 'required|exists:hospitals,id',
            'blood_group' => 'required|in:' . implode(',', BloodGroup::values()),
            'units_donated' => 'required|integer|min:1|max:5',
            'donation_date' => 'required|date',
            'remarks' => 'nullable|string',
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

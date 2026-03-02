<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DonationRequest extends FormRequest
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
            'donorId' => 'required|exists:donors,id',
            'hospitalId' => 'required|exists:hospitals,id',
            'bloodRequestId' => 'nullable|exists:blood_requests,id',
            'createdBy' => 'required|exists:users,id',
            'bloodGroup' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'unitsDonated' => 'required|integer|min:1|max:10',
            'donationDate' => 'required|date',
            'status' => 'required|in:pending,cancelled,approved,screening,rejected,completed',
            'approvedBy' => 'nullable|exists:users,id',
            'approvedAt' => 'nullable|date',
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

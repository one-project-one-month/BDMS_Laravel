<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CertificateRequest extends FormRequest
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
            'user_id' => $this->userId,
            'certificate_title' => $this->certificateTitle,
            'certificate_description' => $this->certificateDescription,
            'certificate_date' => $this->certificateDate,
            'certificate_image' => $this->file('certificateImage') ?? $this->certificateImage,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'certificate_title' => 'required|string|max:255',
            'certificate_description' => 'required|string',
            'certificate_date' => 'required|date|before_or_equal:today',
        ];

        if ($this->isMethod('post')) {
            $rules['certificate_image'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['certificate_image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }

        return $rules;
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

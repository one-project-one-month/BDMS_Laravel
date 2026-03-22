<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class AnnouncementRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('isActive')
                ? filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN)
                : true,

            'expired_at' => $this->expiredAt ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_active' => ['boolean'],
            'expired_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}

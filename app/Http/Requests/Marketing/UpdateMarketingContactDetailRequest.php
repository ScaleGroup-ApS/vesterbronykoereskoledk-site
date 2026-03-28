<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketingContactDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:80'],
            'phone_href' => ['required', 'string', 'max:40'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'opening_hours' => ['nullable', 'string', 'max:5000'],
            'address_line' => ['nullable', 'string', 'max:500'],
        ];
    }
}

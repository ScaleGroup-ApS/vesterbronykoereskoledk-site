<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketingHomeCopyRequest extends FormRequest
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
            'hero_headline_prefix' => ['required', 'string', 'max:120'],
            'hero_headline_accent' => ['required', 'string', 'max:120'],
            'hero_subtitle' => ['nullable', 'string', 'max:2000'],
            'why_title' => ['required', 'string', 'max:120'],
            'why_lead' => ['nullable', 'string', 'max:2000'],
            'reviews_title' => ['required', 'string', 'max:120'],
            'reviews_lead' => ['nullable', 'string', 'max:2000'],
            'reviews_footnote' => ['nullable', 'string', 'max:500'],
            'explore_title' => ['required', 'string', 'max:120'],
            'explore_lead' => ['nullable', 'string', 'max:2000'],
            'cta_title' => ['required', 'string', 'max:120'],
            'cta_lead' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

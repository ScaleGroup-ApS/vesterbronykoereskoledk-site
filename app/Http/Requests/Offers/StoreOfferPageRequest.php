<?php

namespace App\Http\Requests\Offers;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isInstructor();
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'attachment' => ['nullable', 'file', 'max:51200', 'mimes:pdf,doc,docx,zip'],
        ];
    }
}

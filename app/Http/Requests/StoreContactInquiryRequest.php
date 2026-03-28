<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class StoreContactInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $offerId = $this->input('offer_id');

        $this->merge([
            'offer_id' => $offerId === '' || $offerId === null ? null : (int) $offerId,
            'phone' => $this->filled('phone') ? (string) $this->input('phone') : null,
            'message' => $this->filled('message') ? (string) $this->input('message') : null,
            'preferred_hold_start' => $this->filled('preferred_hold_start')
                ? (string) $this->input('preferred_hold_start')
                : null,
        ]);
    }

    /**
     * @return array<string, array<int, mixed|string>>
     */
    public function rules(): array
    {
        $holdValues = Collection::make(config('marketing.hold_start_options', []))
            ->pluck('value')
            ->filter()
            ->values()
            ->all();

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:5000'],
            'offer_id' => ['nullable', 'integer', 'exists:offers,id'],
            'preferred_hold_start' => ['nullable', 'string', 'max:64', Rule::in($holdValues)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Skriv dit navn.',
            'email.required' => 'Skriv din e-mail.',
            'email.email' => 'Angiv en gyldig e-mail.',
            'offer_id.exists' => 'Den valgte pakke findes ikke.',
            'preferred_hold_start.in' => 'Vælg et gyldigt tidspunkt for holdstart.',
        ];
    }
}

<?php

namespace App\Http\Requests\Offers;

use App\Enums\OfferType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'track_required' => $this->boolean('track_required'),
            'slippery_required' => $this->boolean('slippery_required'),
            'requires_theory_exam' => $this->boolean('requires_theory_exam'),
            'requires_practical_exam' => $this->boolean('requires_practical_exam'),
        ]);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'string', Rule::enum(OfferType::class)],
            'theory_lessons' => ['nullable', 'integer', 'min:0'],
            'driving_lessons' => ['nullable', 'integer', 'min:0'],
            'track_required' => ['nullable', 'boolean'],
            'slippery_required' => ['nullable', 'boolean'],
            'requires_theory_exam' => ['nullable', 'boolean'],
            'requires_practical_exam' => ['nullable', 'boolean'],
        ];
    }
}

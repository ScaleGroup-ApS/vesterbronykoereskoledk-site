<?php

namespace App\Http\Requests\Courses;

use App\Enums\OfferType;
use App\Models\Offer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $offer = Offer::query()->findOrFail($this->integer('offer_id'));

        return $this->user()->can('update', $offer);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('public_spots_remaining') && $this->input('public_spots_remaining') === '') {
            $this->merge(['public_spots_remaining' => null]);
        }

        if ($this->has('max_students') && $this->input('max_students') === '') {
            $this->merge(['max_students' => null]);
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('offers', 'id')->where('type', OfferType::Primary->value),
            ],
            'start_at' => ['required', 'date', 'after:now'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'featured_on_home' => ['sometimes', 'boolean'],
            'public_spots_remaining' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

<?php

namespace App\Http\Requests\Courses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('course')->offer);
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
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'featured_on_home' => ['sometimes', 'boolean'],
            'public_spots_remaining' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

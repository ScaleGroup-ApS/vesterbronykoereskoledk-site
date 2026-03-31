<?php

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isInstructor();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'instructor_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

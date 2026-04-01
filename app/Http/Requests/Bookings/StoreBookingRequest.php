<?php

namespace App\Http\Requests\Bookings;

use App\Enums\BookingType;
use App\Rules\NoBookingConflict;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
        $starts = (string) $this->input('starts_at', '');
        $ends = (string) $this->input('ends_at', '');

        return [
            'student_id' => ['required', 'integer', 'exists:students,id',
                new NoBookingConflict('student_id', $starts, $ends, 'Eleven har allerede en booking i dette tidsrum.')],
            'instructor_id' => ['required', 'integer', 'exists:users,id',
                new NoBookingConflict('instructor_id', $starts, $ends, 'Instruktøren har allerede en booking i dette tidsrum.')],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id',
                new NoBookingConflict('vehicle_id', $starts, $ends, 'Køretøjet er allerede booket i dette tidsrum.')],
            'type' => ['required', 'string', Rule::enum(BookingType::class)],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

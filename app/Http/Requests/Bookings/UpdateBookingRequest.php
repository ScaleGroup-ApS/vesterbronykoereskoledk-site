<?php

namespace App\Http\Requests\Bookings;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Rules\NoBookingConflict;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
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
            'instructor_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'vehicle_id' => ['sometimes', 'nullable', 'integer', 'exists:vehicles,id'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['sometimes', 'date', 'after:starts_at'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', Rule::enum(BookingStatus::class)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        // Status-only updates don't move the booking, so no conflict check needed.
        if ($this->has('status') && ! $this->hasAny(['starts_at', 'ends_at', 'instructor_id', 'vehicle_id'])) {
            return;
        }

        $validator->after(function (Validator $v) {
            /** @var Booking $booking */
            $booking = $this->route('booking');

            $starts = $this->input('starts_at', $booking->starts_at->toDateTimeString());
            $ends = $this->input('ends_at', $booking->ends_at->toDateTimeString());
            $instructorId = $this->has('instructor_id') ? $this->input('instructor_id') : $booking->instructor_id;
            $vehicleId = $this->has('vehicle_id') ? $this->input('vehicle_id') : $booking->vehicle_id;

            $rules = [
                ['student_id', $booking->student_id, 'Eleven har allerede en booking i dette tidsrum.'],
                ['instructor_id', $instructorId, 'Instruktøren har allerede en booking i dette tidsrum.'],
                ['vehicle_id', $vehicleId, 'Køretøjet er allerede booket i dette tidsrum.'],
            ];

            foreach ($rules as [$column, $value, $message]) {
                $conflict = (new NoBookingConflict($column, $starts, $ends, $message, $booking->id));
                $conflict->validate($column, $value, fn ($msg) => $v->errors()->add($column, $msg));
            }
        });
    }
}

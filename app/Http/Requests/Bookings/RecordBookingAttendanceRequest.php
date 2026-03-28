<?php

namespace App\Http\Requests\Bookings;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;

class RecordBookingAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $booking = $this->route('booking');

        return $booking instanceof Booking && $this->user()->can('recordAttendance', $booking);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'attended' => ['required', 'boolean'],
        ];
    }
}

<?php

namespace App\Rules;

use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoBookingConflict implements ValidationRule
{
    public function __construct(
        private readonly string $column,
        private readonly string $startsAt,
        private readonly string $endsAt,
        private readonly string $message,
        private readonly ?int $excludeBookingId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        $conflict = Booking::query()
            ->where($this->column, $value)
            ->overlapping($this->startsAt, $this->endsAt)
            ->when($this->excludeBookingId, fn ($q) => $q->where('id', '!=', $this->excludeBookingId))
            ->exists();

        if ($conflict) {
            $fail($this->message);
        }
    }
}

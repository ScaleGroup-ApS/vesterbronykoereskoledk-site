<?php

namespace App\Livewire;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class BookingCalendar extends Component
{
    public int $month;

    public int $year;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    /**
     * @return array<string, array<int, Booking>>
     */
    public function getBookingsByDateProperty(): array
    {
        $start = Carbon::create($this->year, $this->month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return Booking::with(['student.user', 'vehicle', 'instructor'])
            ->whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (Booking $booking) => $booking->starts_at->format('Y-m-d'))
            ->toArray();
    }

    public function getBlankDaysProperty(): int
    {
        // Monday = 1 in dayOfWeekIso, so blank cells = dayOfWeekIso - 1 (Mon=0 .. Sun=6)
        return Carbon::create($this->year, $this->month, 1)->dayOfWeekIso - 1;
    }

    public function getCalendarDatesProperty(): array
    {
        $start = Carbon::create($this->year, $this->month)->startOfMonth();

        return range(1, $start->daysInMonth);
    }

    public function getMonthNameProperty(): string
    {
        return Carbon::create($this->year, $this->month)->translatedFormat('F');
    }

    public function render(): View
    {
        return view('livewire.booking-calendar', [
            'bookingsByDate' => $this->bookingsByDate,
            'blankDays' => $this->blankDays,
            'calendarDates' => $this->calendarDates,
            'monthName' => $this->monthName,
        ]);
    }
}

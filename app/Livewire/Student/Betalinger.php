<?php

namespace App\Livewire\Student;

use App\Actions\Payments\CalculateBalance;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class Betalinger extends Component
{
    public function render(CalculateBalance $calculateBalance): View
    {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return view('livewire.student.betalinger', [
                'balance' => null,
                'payments' => [],
                'offerPrices' => [],
            ]);
        }

        $tz = config('app.timezone');

        $payments = Payment::where('student_id', $student->id)
            ->orderByDesc('recorded_at')
            ->get()
            ->map(fn (Payment $p) => [
                'amount' => (float) $p->amount,
                'method_label' => $this->methodLabel($p->method->value),
                'recorded_at' => $p->recorded_at->timezone($tz)->translatedFormat('d. M Y'),
                'notes' => $p->notes,
            ])
            ->values()
            ->all();

        $student->loadMissing('offers');
        $offerPrices = $student->offers->map(fn ($offer) => [
            'name' => $offer->name,
            'price' => (float) $offer->price,
        ])->values()->all();

        return view('livewire.student.betalinger', [
            'balance' => $calculateBalance->handle($student),
            'payments' => $payments,
            'offerPrices' => $offerPrices,
        ]);
    }

    private function methodLabel(string $method): string
    {
        return match ($method) {
            'cash' => 'Kontant',
            'card' => 'Kort',
            'mobile_pay' => 'MobilePay',
            'invoice' => 'Faktura',
            default => $method,
        };
    }
}

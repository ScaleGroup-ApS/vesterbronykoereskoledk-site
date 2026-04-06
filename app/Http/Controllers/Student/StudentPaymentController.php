<?php

namespace App\Http\Controllers\Student;

use App\Actions\Payments\CalculateBalance;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentPaymentController extends Controller
{
    public function __invoke(Request $request, CalculateBalance $calculateBalance): Response
    {
        $student = $this->student($request);

        $balance = $calculateBalance->handle($student);

        $payments = Payment::where('student_id', $student->id)
            ->orderByDesc('recorded_at')
            ->get()
            ->map(fn (Payment $p) => [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'method' => $p->method->value,
                'method_label' => $this->methodLabel($p->method->value),
                'recorded_at' => $p->recorded_at->toIso8601String(),
                'notes' => $p->notes,
            ]);

        $offerPrices = $student->offers->map(fn ($offer) => [
            'name' => $offer->name,
            'price' => (float) $offer->price,
        ])->values();

        return Inertia::render('student/payments', [
            'balance' => $balance,
            'payments' => $payments,
            'offer_prices' => $offerPrices,
        ]);
    }

    private function student(Request $request): Student
    {
        $student = $request->user()->student;
        abort_unless($student, 404);
        $student->loadMissing('offers');

        return $student;
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

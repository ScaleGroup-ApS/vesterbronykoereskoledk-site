<?php

namespace App\Http\Controllers\Payments;

use App\Actions\Payments\RecordPayment;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with('student.user');

        if ($search = $request->input('search')) {
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }

        $payments = $query->latest('recorded_at')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('payments/index', [
            'payments' => $payments,
            'filters' => [
                'search' => $request->input('search', ''),
                'method' => $request->input('method', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Payment::class);

        return Inertia::render('payments/create', [
            'students' => Student::with('user')->get(),
            'paymentMethods' => collect(PaymentMethod::cases())->map(fn ($m) => [
                'value' => $m->value,
                'label' => $m->name,
            ]),
        ]);
    }

    public function store(StorePaymentRequest $request, RecordPayment $action): RedirectResponse
    {
        $action->handle($request->validated());

        return redirect()->route('payments.index')
            ->with('success', 'Betaling registreret.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $this->authorize('delete', $payment);

        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Betaling slettet.');
    }
}

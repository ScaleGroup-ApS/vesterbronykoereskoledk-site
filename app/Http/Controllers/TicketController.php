<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketCommentRequest;
use App\Http\Requests\CreateTicketRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(): Response
    {
        $tickets = Http::crm()
            ->get('/api/driving-schools/tickets', ['customerId' => config('services.crm.customer_id')])
            ->json();

        return Inertia::render('Support/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function store(CreateTicketRequest $request): RedirectResponse
    {
        Http::crm()->post('/api/driving-schools/tickets', [
            'customerId' => (int) config('services.crm.customer_id'),
            'subject' => $request->validated('subject'),
            'initialMessage' => $request->validated('message'),
            'priority' => $request->validated('priority'),
        ]);

        return redirect()->route('support.index')
            ->with('success', 'Ticket oprettet.');
    }

    public function show(int $ticketId): Response
    {
        $ticket = Http::crm()
            ->get("/api/driving-schools/tickets/{$ticketId}", ['customerId' => config('services.crm.customer_id')])
            ->json();

        return Inertia::render('Support/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function addComment(CreateTicketCommentRequest $request, int $ticketId): RedirectResponse
    {
        Http::crm()->post("/api/driving-schools/tickets/{$ticketId}/comments", [
            'customerId' => (int) config('services.crm.customer_id'),
            'message' => $request->validated('message'),
        ]);

        return redirect()->route('support.show', $ticketId)
            ->with('success', 'Svar sendt.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketCommentRequest;
use App\Http\Requests\CreateTicketRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class TicketController extends Controller
{
    public function index(): Response
    {
        abort(501, 'Support page not yet implemented in Filament.');
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
        abort(501, 'Support page not yet implemented in Filament.');
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

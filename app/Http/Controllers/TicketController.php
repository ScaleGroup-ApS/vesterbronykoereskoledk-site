<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketCommentRequest;
use App\Http\Requests\CreateTicketRequest;
use App\Services\CrmTicketService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function __construct(private readonly CrmTicketService $crm) {}

    public function index(): Response
    {
        $tickets = $this->crm->getTickets();

        return Inertia::render('Support/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function store(CreateTicketRequest $request): RedirectResponse
    {
        $this->crm->createTicket(
            subject: $request->validated('subject'),
            message: $request->validated('message'),
            priority: $request->validated('priority'),
        );

        return redirect()->route('support.index')
            ->with('success', 'Ticket oprettet.');
    }

    public function show(int $ticketId): Response
    {
        $ticket = $this->crm->getTicket($ticketId);

        return Inertia::render('Support/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function addComment(CreateTicketCommentRequest $request, int $ticketId): RedirectResponse
    {
        $this->crm->addComment(
            ticketId: $ticketId,
            message: $request->validated('message'),
        );

        return redirect()->route('support.show', $ticketId)
            ->with('success', 'Svar sendt.');
    }
}

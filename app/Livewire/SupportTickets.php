<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SupportTickets extends Component
{
    public string $view = 'list';

    public ?int $activeTicketId = null;

    public string $subject = '';

    public string $message = '';

    public string $priority = 'normal';

    public string $commentMessage = '';

    /** @var array<int, array<string, mixed>> */
    public array $tickets = [];

    /** @var array<string, mixed> */
    public array $activeTicket = [];

    public function mount(): void
    {
        $this->loadTickets();
    }

    public function loadTickets(): void
    {
        $customerId = (int) config('services.crm.customer_id');

        $response = Http::crm()->get('/api/driving-schools/tickets', [
            'customerId' => $customerId,
        ]);

        $this->tickets = $response->successful() ? $response->json('data', []) : [];
    }

    public function showCreateForm(): void
    {
        $this->view = 'create';
        $this->reset(['subject', 'message', 'priority']);
        $this->priority = 'normal';
    }

    public function showList(): void
    {
        $this->view = 'list';
        $this->activeTicketId = null;
        $this->activeTicket = [];
        $this->loadTickets();
    }

    public function createTicket(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|string|in:low,normal,high,urgent',
        ]);

        Http::crm()->post('/api/driving-schools/tickets', [
            'customerId' => (int) config('services.crm.customer_id'),
            'subject' => $this->subject,
            'initialMessage' => $this->message,
            'priority' => $this->priority,
        ]);

        $this->reset(['subject', 'message', 'priority']);
        $this->priority = 'normal';

        session()->flash('success', 'Ticket oprettet.');
        $this->showList();
    }

    public function viewTicket(int $ticketId): void
    {
        $customerId = (int) config('services.crm.customer_id');

        $response = Http::crm()->get("/api/driving-schools/tickets/{$ticketId}", [
            'customerId' => $customerId,
        ]);

        if ($response->successful()) {
            $this->activeTicket = $response->json('data', []);
            $this->activeTicketId = $ticketId;
            $this->view = 'show';
            $this->commentMessage = '';
        }
    }

    public function addComment(): void
    {
        $this->validate([
            'commentMessage' => 'required|string',
        ]);

        Http::crm()->post("/api/driving-schools/tickets/{$this->activeTicketId}/comments", [
            'customerId' => (int) config('services.crm.customer_id'),
            'message' => $this->commentMessage,
        ]);

        $this->commentMessage = '';

        session()->flash('success', 'Svar sendt.');
        $this->viewTicket($this->activeTicketId);
    }

    public function render(): View
    {
        return view('livewire.support-tickets');
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CrmTicketService
{
    private string $baseUrl;

    private string $apiKey;

    private int $customerId;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.crm.url'), '/');
        $this->apiKey = (string) config('services.crm.api_key');
        $this->customerId = (int) config('services.crm.customer_id');
    }

    /**
     * Create a ticket in the CRM.
     *
     * @return array<string, mixed>
     */
    public function createTicket(string $subject, string $message, string $priority = 'normal'): array
    {
        $response = $this->call('tickets.createFromExternal', [
            'customerId' => $this->customerId,
            'subject' => $subject,
            'initialMessage' => $message,
            'priority' => $priority,
            'origin' => 'Køreskole',
        ]);

        return $response->json('result.data', []);
    }

    /**
     * Fetch all tickets for this school's CRM customer.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTickets(): array
    {
        $response = $this->call('tickets.getByCustomerExternal', [
            'customerId' => $this->customerId,
        ]);

        return $response->json('result.data', []);
    }

    /**
     * Fetch a single ticket with full thread details.
     *
     * @return array<string, mixed>
     */
    public function getTicket(int $ticketId): array
    {
        $response = $this->call('tickets.getByIdExternal', [
            'ticketId' => $ticketId,
            'customerId' => $this->customerId,
        ]);

        return $response->json('result.data', []);
    }

    /**
     * Add a staff comment to a ticket.
     *
     * @return array<string, mixed>
     */
    public function addComment(int $ticketId, string $message): array
    {
        $response = $this->call('tickets.addCommentExternal', [
            'ticketId' => $ticketId,
            'customerId' => $this->customerId,
            'message' => $message,
        ]);

        return $response->json('result.data', []);
    }

    /**
     * POST to a tRPC mutation procedure.
     * tRPC expects the body as {"json": input}.
     */
    private function call(string $procedure, array $input): Response
    {
        $url = $this->baseUrl.'/api/trpc/'.$procedure;

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, ['json' => $input]);

        if ($response->clientError() || $response->serverError()) {
            throw new RuntimeException(
                "CRM API call to [{$procedure}] failed ({$response->status()}): ".$response->body()
            );
        }

        return $response;
    }
}

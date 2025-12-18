<?php

use App\Models\Ticket;

class TicketService
{
    public function create(array $data, int $userId): Ticket
    {
        return Ticket::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'open',
            'created_by' => $userId,
        ]);
    }
}

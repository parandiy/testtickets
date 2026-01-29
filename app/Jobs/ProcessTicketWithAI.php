<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\AI\AIClientInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTicketWithAI implements ShouldQueue
{
    use Dispatchable;

    public int $tries = 3;
    public int $backoff = 10;


    public function __construct(
        private int $ticketId
    ) {}

    public function handle(AIClientInterface $ai): void
    {
        $ticket = Ticket::find($this->ticketId);

        if (!$ticket) {
            return;
        }

        $result = $ai->analyze($ticket->description);

        $ticket->update([
            'category' => $result->category,
            'sentiment' => $result->sentiment->value,
            'urgency' => $result->urgency->value,
            'suggested_reply' => $result->reply,
        ]);
    }
}

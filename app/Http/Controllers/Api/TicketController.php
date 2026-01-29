<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTicketWithAI;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::create($data);

        ProcessTicketWithAI::dispatch($ticket->id);

        return response()->json([
            'message' => 'Ticket created',
            'ticket_id' => $ticket->id,
        ], 201);
    }

    public function show(int $id)
    {
        return Ticket::findOrFail($id);
    }
}

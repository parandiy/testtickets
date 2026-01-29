<?php

namespace Tests\Feature;

use App\Dto\TicketAIResultDto;
use App\Enums\Sentiment;
use App\Enums\Urgency;
use App\Jobs\ProcessTicketWithAI;
use App\Models\Ticket;
use App\Services\AI\AIClientInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_creation_flow(): void
    {
        $response = $this->postJson('/api/tickets', [
            'title' => 'Payment issue',
            'description' => 'I am angry because my payment failed',
        ]);

        $response->assertStatus(201);

        $ticketId = $response->json('ticket_id');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticketId,
            'status' => 'Open',
        ]);
    }

    public function test_ai_job_enriches_ticket_using_enums_and_dto(): void
    {
        $ticket = Ticket::create([
            'title' => 'Payment failed',
            'description' => 'I am VERY ANGRY because my payment did not go through',
        ]);

        $this->assertNull($ticket->sentiment);
        $this->assertNull($ticket->urgency);

        $job = new ProcessTicketWithAI($ticket->id);
        $job->handle(app()->make(AIClientInterface::class));

        $ticket->refresh();

        $this->assertSame('Billing', $ticket->category);
        $this->assertSame(Sentiment::Negative->value, $ticket->sentiment);
        $this->assertSame(Urgency::High->value, $ticket->urgency);

        $this->assertNotEmpty($ticket->suggested_reply);
    }

    public function test_fail_safe_mapping_accepts_llm_variations(): void
    {
        $this->app->bind(AIClientInterface::class, fn () =>
        new class implements AIClientInterface {
            public function analyze(string $text): TicketAIResultDto
            {
                return TicketAIResultDto::fromArray([
                    'category' => 'Billing',
                    'sentiment' => 'NEGATIVE',
                    'urgency' => 'URGENT',
                    'reply' => 'We are handling your issue.',
                ]);
            }
        }
        );

        $ticket = Ticket::create([
            'title' => 'Payment issue',
            'description' => 'Payment failed',
        ]);

        (new ProcessTicketWithAI($ticket->id))
            ->handle(app()->make(AIClientInterface::class));

        $ticket->refresh();

        $this->assertSame(Sentiment::Negative->value, $ticket->sentiment);
        $this->assertSame(Urgency::High->value, $ticket->urgency);
    }

    public function test_sentiment_llm_mapping(): void
    {
        $this->assertEquals(Sentiment::Negative, Sentiment::fromLLM('NEGATIVE'));
        $this->assertEquals(Sentiment::Negative, Sentiment::fromLLM('angry'));
        $this->assertEquals(Sentiment::Neutral, Sentiment::fromLLM('ok'));
    }
}

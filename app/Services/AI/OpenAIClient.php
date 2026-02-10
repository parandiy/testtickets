<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Enums\Sentiment;
use App\Enums\Urgency;
use App\Dto\TicketAIResultDto;
use App\Services\AI\AIClientInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class OpenAIClient implements AIClientInterface
{
    private const ENDPOINT = 'https://api.openai.com/v1/chat/completions';

    public function analyze(string $text): TicketAIResultDto
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post(self::ENDPOINT, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            if($response->getStatusCode() === 429) {
                throw new RuntimeException('OpenAI request failed. Too many requests.');
            }

            throw new RuntimeException('OpenAI request failed');
        }

        $content = $response->json('choices.0.message.content');

        if (!is_string($content)) {
            throw new RuntimeException('Invalid OpenAI response');
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new RuntimeException('Invalid JSON returned by OpenAI');
        }

        return new TicketAIResultDto(
            category: (string) ($data['category'] ?? 'General'),
            sentiment: $this->mapSentiment($data['sentiment'] ?? null),
            urgency: $this->mapUrgency($data['urgency'] ?? null),
            reply: (string) ($data['suggested_reply'] ?? '')
        );
    }

    private function mapSentiment(?string $value): Sentiment
    {
        return match (strtoupper((string) $value)) {
            'POSITIVE' => Sentiment::Positive,
            'NEGATIVE' => Sentiment::Negative,
            'NEUTRAL'  => Sentiment::Neutral,
            default    => Sentiment::Neutral,
        };
    }

    private function mapUrgency(?string $value): Urgency
    {
        return match (strtoupper((string) $value)) {
            'LOW'    => Urgency::Low,
            'MEDIUM' => Urgency::Normal,
            'HIGH'   => Urgency::High,
            default  => Urgency::Normal,
        };
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
            You are a Helpful Customer Support Agent.
            Analyze the user's ticket.

            Return ONLY valid JSON.
            No markdown.
            No explanations.

            JSON format:
            {
              "category": "Technical | Billing | General",
              "sentiment": "Positive | Neutral | Negative",
              "urgency": "Low | Normal | High",
              "reply": "Suggested reply to the user"
            }
            PROMPT;
    }
}

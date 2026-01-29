<?php

namespace App\Dto;

use App\Enums\Sentiment;
use App\Enums\Urgency;
use InvalidArgumentException;

class TicketAIResultDto
{
    public function __construct(
        public readonly string $category,
        public readonly Sentiment $sentiment,
        public readonly Urgency $urgency,
        public readonly string $reply,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (!in_array($this->category, ['Technical', 'Billing', 'General'], true)) {
            throw new InvalidArgumentException('Invalid category value');
        }

        if (trim($this->reply) === '') {
            throw new InvalidArgumentException('Reply cannot be empty');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            category: $data['category'] ?? '',
            sentiment: Sentiment::fromLLM($data['sentiment'] ?? ''),
            urgency: Urgency::fromLLM($data['urgency'] ?? ''),
            reply: $data['reply'] ?? '',
        );
    }
}

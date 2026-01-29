<?php

namespace App\Services\AI;

use App\Dto\TicketAIResultDto;
use App\Enums\Sentiment;
use App\Enums\Urgency;

class FakeAIClient implements AIClientInterface
{
    public function analyze(string $text): TicketAIResultDto
    {
        sleep(2);

        $isAngry = str_contains(strtolower($text), 'angry');
        $isBilling = str_contains(strtolower($text), 'pay');

        return new TicketAIResultDto(
            category: $isBilling ? 'Billing' : 'Technical',
            sentiment: $isAngry ? Sentiment::Negative : Sentiment::Neutral,
            urgency: $isAngry ? Urgency::High : Urgency::Normal,
            reply: 'Thank you for contacting support. We are reviewing your issue.'
        );
    }
}

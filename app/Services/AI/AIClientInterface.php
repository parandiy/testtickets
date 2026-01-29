<?php

namespace App\Services\AI;

use App\Dto\TicketAIResultDto;

interface AIClientInterface
{
    public function analyze(string $text): TicketAIResultDto;
}

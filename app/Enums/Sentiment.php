<?php

namespace App\Enums;

use InvalidArgumentException;

enum Sentiment: string
{
    case Positive = 'Positive';
    case Neutral  = 'Neutral';
    case Negative = 'Negative';

    public static function fromLLM(string $value): self
    {
        $normalized = strtolower(trim($value));

        return match (true) {
            in_array($normalized, ['positive', 'pos', 'good', 'happy']) => self::Positive,
            in_array($normalized, ['neutral', 'ok', 'normal'])          => self::Neutral,
            in_array($normalized, ['negative', 'neg', 'bad', 'angry'])  => self::Negative,
            default => throw new InvalidArgumentException(
                "Unknown sentiment value from LLM: {$value}"
            ),
        };
    }
}

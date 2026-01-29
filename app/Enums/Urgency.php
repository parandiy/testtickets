<?php

namespace App\Enums;

use InvalidArgumentException;

enum Urgency: string
{
    case Low    = 'Low';
    case Normal = 'Normal';
    case High   = 'High';

    public static function fromLLM(string $value): self
    {
        $normalized = strtolower(trim($value));

        return match (true) {
            in_array($normalized, ['low', 'minor'])               => self::Low,
            in_array($normalized, ['normal', 'medium', 'default']) => self::Normal,
            in_array($normalized, ['high', 'urgent', 'critical'])  => self::High,
            default => throw new InvalidArgumentException(
                "Unknown urgency value from LLM: {$value}"
            ),
        };
    }
}

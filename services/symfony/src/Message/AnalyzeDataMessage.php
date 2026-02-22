<?php

namespace App\Message;

class AnalyzeDataMessage
{
    public function __construct(
        public readonly int $soldierId,
        public readonly array $stats
    ) {}
}
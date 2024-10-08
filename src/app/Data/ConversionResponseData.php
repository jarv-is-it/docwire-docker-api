<?php

namespace App\Data;

class ConversionResponseData
{
    public function __construct(
        public bool $success,
        public int $code,
        public string $content,
        public ?string $command = null,
    ) {}
}

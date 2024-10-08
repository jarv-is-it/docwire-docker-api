<?php

namespace App\Data;

use App\Enums\ConversionOutput;

class AsyncConversionData
{
    public function __construct(
        public string $filename,
        public string $content,
        public string $identifier,
        public string $webhook,
        public ConversionOutput $output = ConversionOutput::PlainText,
        public string $language = 'eng',
    ) {}
}

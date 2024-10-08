<?php

namespace App\Enums;

enum ConversionOutput: string
{
    case PlainText = 'plain_text';
    case Html = 'html';
    case Csv = 'csv';
    case Metadata = 'metadata';
}

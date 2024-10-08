<?php

namespace App\Components;

use App\Components\Helper;
use App\Data\ConversionResponseData;
use App\Enums\ConversionOutput;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Convert
{
    protected const FILENAME = 'content';

    public static function execute($filename, $content, ConversionOutput $output = ConversionOutput::PlainText, $language = 'eng'): ConversionResponseData
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $directory = (new TemporaryDirectory())->create();
        $path = $directory->path(static::FILENAME . '.' . $extension);

        file_put_contents($path, $content);

        $command = sprintf('"%s" --input-file "%s" --output_type %s --language %s', config('services.docwire.path'),  $path, $output->value, $language);

        exec($command, $output, $resultCode);

        $content = Helper::fixEncoding(implode(PHP_EOL, $output));

        $directory->delete();

        return new ConversionResponseData(
            ($resultCode == 0),
            $resultCode,
            $content,
            $command,
        );
    }
}

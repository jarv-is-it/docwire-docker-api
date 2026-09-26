<?php

namespace App\Components;

use App\Components\Helper;
use App\Data\ConversionResponseData;
use App\Enums\ConversionOutput;
use League\HTMLToMarkdown\Converter\TableConverter;
use League\HTMLToMarkdown\HtmlConverter;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Convert
{
    protected const FILENAME = 'content';

    public static function make()
    {
        return new static();
    }

    public function execute(string $filename, string $content, ConversionOutput $output = ConversionOutput::PlainText, string $language = 'eng'): ConversionResponseData
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $directory = (new TemporaryDirectory())->create();
        $path = $directory->path(static::FILENAME . '.' . $extension);

        file_put_contents($path, $content);

        if ($extension === 'pdf') {
            $decryptedPath = $directory->path(static::FILENAME . '-decrypted.pdf');
            $this->decryptPdf($path, $decryptedPath);
            $path = $decryptedPath;
        }

        $outputParam = match ($output) {
            ConversionOutput::Markdown => 'html',
            default => $output->value,
        };

        $command = sprintf('"%s" --input-file "%s" --output_type %s --language %s', config('services.docwire.path'),  $path, $outputParam, $language);

        exec($command, $result, $resultCode);

        $content = Helper::fixEncoding(implode(PHP_EOL, $result));

        $directory->delete();

        if ($output === ConversionOutput::Markdown) {
            $content = $this->convertToMarkdown($content);
        }

        return new ConversionResponseData(
            ($resultCode == 0),
            $resultCode,
            $content,
            $command,
        );
    }

    protected function decryptPdf(string $pdfFile, string $outputFile): void
    {
        if (! is_readable($pdfFile)) {
            throw new \RuntimeException("PDF file not found or not readable: {$pdfFile}");
        }

        $command = 'qpdf ' . escapeshellarg($pdfFile)
            . ' --remove-restrictions --decrypt ' . escapeshellarg($outputFile);

        exec($command, $cmdOutput, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException("Failed to decrypt {$pdfFile} (exit code: {$exitCode})");
        }
    }

    protected function convertToMarkdown($content)
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'strip_placeholder_links' => true,
            'use_autolinks' => false,
            'hard_break' => true,
            'remove_nodes' => 'script style iframe',
        ]);

        $converter->getEnvironment()->addConverter(new TableConverter());

        return $converter->convert($content);
    }
}

<?php

namespace Tests\Unit;

use App\Components\Convert;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ConvertDecryptTest extends TestCase
{
    public function test_decrypt_pdf_writes_decrypted_output(): void
    {
        if (! $this->qpdfIsAvailable()) {
            $this->markTestSkipped('qpdf is not installed');
        }

        $directory = (new TemporaryDirectory())->create();
        $input = $directory->path('input.pdf');
        $output = $directory->path('output.pdf');

        exec('qpdf --empty ' . escapeshellarg($input), $cmdOutput, $exitCode);
        $this->assertSame(0, $exitCode, 'failed to create empty PDF fixture');

        $this->makeConvert()->decrypt($input, $output);

        $this->assertFileExists($output);
        $this->assertGreaterThan(0, filesize($output));

        $directory->delete();
    }

    public function test_decrypt_pdf_throws_when_input_is_missing(): void
    {
        $this->expectException(RuntimeException::class);

        $this->makeConvert()->decrypt('/tmp/does-not-exist-' . uniqid() . '.pdf', '/tmp/out.pdf');
    }

    private function makeConvert(): Convert
    {
        return new class extends Convert {
            public function decrypt(string $pdfFile, string $outputFile): void
            {
                $this->decryptPdf($pdfFile, $outputFile);
            }
        };
    }

    private function qpdfIsAvailable(): bool
    {
        exec('command -v qpdf', $output, $exitCode);

        return $exitCode === 0;
    }
}

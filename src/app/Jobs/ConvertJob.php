<?php

namespace App\Jobs;

use App\Components\Convert;
use App\Models\Conversion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ConvertJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Conversion $conversion)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $result = Convert::execute(
            $this->conversion->filename,
            $this->conversion->content,
            $this->conversion->output,
            $this->conversion->language
        );

        Http::timeout(10)
            ->retry(3, 5000)
            ->acceptJson()
            ->contentType('application/json')
            ->post($this->conversion->webhook, [
                'success' => $result->success,
                'code' => $result->code,
                'content' => $result->content,
                'command' => $result->command,
            ]);

        $this->conversion->delete();
    }
}

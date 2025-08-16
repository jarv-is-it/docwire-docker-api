<?php

use App\Models\Conversion;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

// Purge old conversions deleting the files
Schedule::call(function () {
    Conversion::whereDate('created_at', '<', now()->subDay())
        ->get()
        ->each(function (Conversion $conversion) {
            $conversion->delete();
        });
})->hourly();

/*
Artisan::command('test:convert', function () {
    $content = base64_encode(Storage::disk('local')->get('sample.docx'));

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post('http://127.0.0.1:9000/api/v1/convert/sync', [
        'filename' => 'sample.docx',
        'content' => $content,
        'output' => 'html',
    ]);

    dump($response->status(), $response->body());
});
//*/

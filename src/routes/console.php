<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

/*
// Test cron execution
Schedule::call(function () {
    file_put_contents(public_path('test.txt'), date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
})->everyMinute();
// */

/*
// Test sync API
Artisan::command('api:test-sync', function () {

    $content = base64_encode(Storage::disk('local')->get('sample.pdf'));

    $response = Http::withHeader('Accept', 'application/json')
        ->post('http://localhost:9000/api/v1/convert/sync', [
            'filename' => 'sample.pdf',
            'content' => $content,
        ]);

    dd($response->json());
});
// */
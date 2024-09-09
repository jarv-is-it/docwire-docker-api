<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

Artisan::command('test:sync', function () {

    $content = base64_encode(Storage::disk('local')->get('sample.pdf'));

    $response = Http::withHeader('Accept', 'application/json')
        ->post('http://localhost:9000/api/v1/convert/sync', [
            'filename' => 'sample.pdf',
            'content' => $content,
        ]);

    dd($response->json());
});

<?php

namespace App\Http\Controllers\Api\v1;

use App\Components\Convert;
use App\Enums\ConversionOutput;
use App\Http\Requests\AsyncConvertRequest;
use App\Http\Requests\SyncConvertRequest;
use App\Jobs\ConvertJob;
use App\Models\Conversion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ConvertController
{
    protected const FILENAME = 'content';

    public function sync(SyncConvertRequest $request)
    {
        $data = $request->validated();

        $data['output'] = ConversionOutput::from($data['output'] ?? 'plain_text');
        $data['language'] = $data['language'] ?? 'eng';
        $data['content'] = base64_decode($data['content']);

        $conversion = Convert::execute(
            $data['filename'],
            $data['content'],
            $data['output'],
            $data['language']
        );

        return response()->json([
            'success' => $conversion->success,
            'code' => $conversion->code,
            'content' => $conversion->content,
            'command' => $conversion->command,
        ]);
    }

    public function async(AsyncConvertRequest $request)
    {
        $data = $request->validated();

        $data['output'] = ConversionOutput::from($data['output'] ?? 'plain_text');
        $data['language'] = $data['language'] ?? 'eng';

        $extension = pathinfo($data['filename'], PATHINFO_EXTENSION);
        $path = sprintf('conversions/%s.%s', Str::random(40), $extension);

        Storage::put($path, base64_decode($data['content']));

        $conversion = Conversion::create([
            'filename' => $data['filename'],
            'extension' => $extension,
            'path' => $path,
            'identifier' => $data['identifier'],
            'webhook' => $data['webhook'],
            'output' => $data['output'],
            'language' => $data['language'],
        ]);

        ConvertJob::dispatch($conversion);

        return response()->json([
            'success' => true,
            'code' => 0,
            'content' => 'Conversion started, wait for the webhook to be called.',
            'command' => null,
        ]);
    }

    // TODO: Remove this!
    /*
    public function webhook(Request $request)
    {
        Storage::put('log.json', json_encode($request->json()->all()));

        return response()->json([
            'done' => true,
        ]);
    }
    // */
    // /TODO: Remove this!
}

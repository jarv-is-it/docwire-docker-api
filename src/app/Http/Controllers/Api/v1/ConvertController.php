<?php

namespace App\Http\Controllers\Api\v1;

use App\Components\Helper;
use App\Http\Requests\ConvertRequest;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ConvertController
{
    protected const FILENAME = 'content';

    public function sync(ConvertRequest $request)
    {
        $data = $request->validated();

        $data['output'] = $data['output'] ?? 'plain_text';
        $data['language'] = $data['language'] ?? 'eng';

        $extension = pathinfo($data['filename'], PATHINFO_EXTENSION);

        $directory = (new TemporaryDirectory())->create();
        $path = $directory->path(static::FILENAME . '.' . $extension);

        file_put_contents($path, base64_decode($data['content']));

        $command = sprintf('"%s" --input-file "%s" --output_type %s --language %s', config('services.docwire.path'),  $path, $data['output'], $data['language']);

        exec($command, $output, $resultCode);

        $content = Helper::fixEncoding(implode(PHP_EOL, $output));

        $directory->delete();

        if ($resultCode != 0) {
            return response()->json([
                'success' => false,
                'code' => $resultCode,
                'message' => $content,
                'command' => $command,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content,
        ]);
    }

    public function async(ConvertRequest $request)
    {
        $data = $request->validated();

        // TODO: Implement this!
    }

    protected function rules()
    {
        return [];
    }
}

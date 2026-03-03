<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MultipartUploadController
{
    use ResolvesS3Disk;

    public function store(Request $request)
    {
        [$client, $bucket] = $this->resolveClientAndBucket($request);

        $response = $client->createMultipartUpload([
            'Bucket' => $bucket,
            'Key' => Str::replaceStart('/', '', $request->input('filename')),
            'ContentType' => $request->input('metadata.type'),
            'ContentDisposition' => 'inline',
        ]);

        return response()->json([
            'uploadId' => $response->get('UploadId'),
            'key' => $response->get('Key'),
        ]);
    }
}

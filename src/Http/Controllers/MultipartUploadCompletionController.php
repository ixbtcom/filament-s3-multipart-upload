<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Illuminate\Http\Request;

class MultipartUploadCompletionController
{
    use ResolvesS3Disk;

    public function store(Request $request, string $uploadId)
    {
        [$client, $bucket] = $this->resolveClientAndBucket($request);

        $result = $client->completeMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $request->query('key'),
            'UploadId' => $uploadId,
            'MultipartUpload' => ['Parts' => $request->input('parts')],
        ]);

        return response()->json([
            'path' => $result->get('Key'),
            'url' => $result->get('Location'),
        ]);
    }
}

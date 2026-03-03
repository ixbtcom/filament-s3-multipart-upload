<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Illuminate\Http\Request;

class TemporarySignedUrlController
{
    use ResolvesS3Disk;

    public function show(Request $request, string $uploadId, int $index)
    {
        [$client, $bucket] = $this->resolveClientAndBucket($request);
        $expiry = config('filament-s3-multipart-upload.expiry', '+1 hour');

        $command = $client->getCommand('uploadPart', [
            'Bucket' => $bucket,
            'Key' => $request->query('key'),
            'UploadId' => $uploadId,
            'PartNumber' => $index,
            'Body' => '',
        ]);

        $url = (string) $client
            ->createPresignedRequest($command, $expiry)
            ->getUri();

        return [
            'url' => $url,
        ];
    }
}

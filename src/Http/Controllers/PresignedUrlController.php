<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PresignedUrlController
{
    use ResolvesS3Disk;

    public function store(Request $request)
    {
        [$client, $bucket] = $this->resolveClientAndBucket($request);

        $key = Str::replaceStart('/', '', $request->input('filename'));
        $contentType = $request->input('contentType', 'application/octet-stream');
        $expiry = config('filament-s3-multipart-upload.expiry', '+1 hour');

        $command = $client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $contentType,
        ]);

        $url = (string) $client
            ->createPresignedRequest($command, $expiry)
            ->getUri();

        return response()->json([
            'method' => 'PUT',
            'url' => $url,
            'headers' => ['Content-Type' => $contentType],
            'key' => $key,
        ]);
    }
}

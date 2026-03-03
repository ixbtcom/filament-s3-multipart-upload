<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;

trait ResolvesS3Disk
{
    protected function resolveClientAndBucket(Request $request): array
    {
        // S3 providers can be slow — extend timeout for multipart operations
        set_time_limit(120);

        $diskName = $request->header('X-S3-Disk')
            ?: config('filament-s3-multipart-upload.disk', 's3');

        // Validate disk exists and is S3
        $diskConfig = config("filesystems.disks.{$diskName}");
        if (! $diskConfig || ($diskConfig['driver'] ?? null) !== 's3') {
            abort(422, "Invalid S3 disk: {$diskName}");
        }

        /** @var S3Client $client */
        $client = app(FilesystemManager::class)->disk($diskName)->getClient();
        $bucket = $diskConfig['bucket'];

        return [$client, $bucket];
    }
}

<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Http\Request;

class TemporarySignedUrlController
{
    public function __construct(private S3Client $s3)
    {
    }

    public function show(Request $request, string $uploadId, int $index)
    {
        $disk = config('filament-s3-multipart-upload.disk', 's3');
        $bucket = config("filesystems.disks.{$disk}.bucket");
        $expiry = config('filament-s3-multipart-upload.expiry', '+1 hour');

        $command = $this->s3->getCommand('uploadPart', [
            'Bucket' => $bucket,
            'Key' => $request->query('key'),
            'UploadId' => $uploadId,
            'PartNumber' => $index,
            'Body' => '',
        ]);

        $url = (string) $this->s3
            ->createPresignedRequest($command, $expiry)
            ->getUri();

        return [
            'url' => $url,
        ];
    }
}

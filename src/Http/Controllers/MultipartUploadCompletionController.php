<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Http\Request;

class MultipartUploadCompletionController
{
    public function __construct(private S3Client $s3)
    {
    }

    public function store(Request $request, string $uploadId)
    {
        $disk = config('filament-s3-multipart-upload.disk', 's3');
        $bucket = config("filesystems.disks.{$disk}.bucket");

        $result = $this->s3->completeMultipartUpload([
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

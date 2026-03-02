<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MultipartUploadController
{
    public function __construct(private S3Client $s3)
    {
    }

    public function store(Request $request)
    {
        $disk = config('filament-s3-multipart-upload.disk', 's3');
        $bucket = config("filesystems.disks.{$disk}.bucket");

        $response = $this->s3->createMultipartUpload([
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

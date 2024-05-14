<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MultipartUploadController
{
    public function __construct(private S3client $s3)
    {
    }

    /**
     * filename
     * type
     * metadata.name
     * metadata.type
     */
    public function store(Request $request)
    {
        $disk = config('filament.uploads.disk');
        $response = $this->s3->createMultipartUpload([
            'Bucket' => config("filesystems.disks.$disk.bucket"),
            'Key' => Str::replaceStart('/','', $request->input('filename')),
            'ContentType' => $request->input('metadata.type'),
            'ContentDisposition' => 'inline',
        ]);

        return response()->json([
            'uploadId' => $response->get('UploadId'),
            'key' => $response->get('Key'),
        ]);
    }
}

<?php

declare(strict_types=1);

use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\MultipartUploadCompletionController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\MultipartUploadController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\PresignedUrlController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\TemporarySignedUrlController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('filament-s3-multipart-upload.prefix', '_multipart-upload') . '/s3')
    ->middleware(config('filament-s3-multipart-upload.middleware', ['web', 'auth']))
    ->name('filament.')
    ->group(function () {
        // Single presigned PUT (for files < multipart threshold)
        Route::post('presign', [PresignedUrlController::class, 'store'])
            ->name('presigned-url.store');

        // Multipart upload (for large files)
        Route::post('multipart', [MultipartUploadController::class, 'store'])
            ->name('multipart-upload.store');

        Route::get('multipart/{uploadId}/{id}', [TemporarySignedUrlController::class, 'show'])
            ->name('multipart-upload.temporary-signed-url.store');

        Route::post('multipart/{uploadId}/complete', [MultipartUploadCompletionController::class, 'store'])
            ->name('multipart-upload.completion.store');
    });

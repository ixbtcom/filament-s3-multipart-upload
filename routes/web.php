<?php

declare(strict_types=1);

use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\MultipartUploadCompletionController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\MultipartUploadController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\TemporarySignedUrlController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('filament-s3-multipart-upload.prefix').'/s3')->name('filament.')->group(function () {

    Route::post('multipart', [MultipartUploadController::class, 'store'])->name('multipart-upload.store');

    Route::get('multipart/{uploadId}/{id}', [TemporarySignedUrlController::class, 'show'])->name('multipart-upload.temporary-signed-url.store');

    Route::post('multipart/{uploadId}/complete', [MultipartUploadCompletionController::class, 'store'])->name('multipart-upload.completion.store');


    /*
     *

    Route::post('multipart', [\CloudMazing\FilamentS3MultipartUpload\Http\Controllers\S3MultipartController::class, 'createMultipartUpload']);
    Route::get('multipart/{uploadId}/{id}', [\CloudMazing\FilamentS3MultipartUpload\Http\Controllers\S3MultipartController::class, 'getUploadedParts']);
    Route::post('multipart/{uploadId}/complete', [\CloudMazing\FilamentS3MultipartUpload\Http\Controllers\S3MultipartController::class, 'completeMultipartUpload']);
     Route::options('multipart',[\CloudMazing\FilamentS3MultipartUpload\Http\Controllers\S3MultipartController::class, 'createPreflightHeader']);
    Route::delete('multipart/{uploadId}/{id}', [\CloudMazing\FilamentS3MultipartUpload\Http\Controllers\S3MultipartController::class, 'abortMultipartUpload']);
    Route::get('multipart/{uploadId}/{partNumber}', [\CloudMazing\FilamentS3MultipartUpload\Http\Controllers\S3MultipartController::class, 'signPartUpload']);*/




}
    );

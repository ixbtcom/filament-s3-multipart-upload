<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload;

use Aws\S3\S3Client;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\MultipartUploadCompletionController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\MultipartUploadController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\S3MultipartController;
use CloudMazing\FilamentS3MultipartUpload\Http\Controllers\TemporarySignedUrlController;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\FilesystemManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentS3MultipartUploadServiceProvider extends PackageServiceProvider
{
    public static string $name = 's3-multipart-upload';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-s3-multipart-upload')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasRoutes('web');
    }

    public function boot(): void
    {
        parent::boot();

        FilamentAsset::register([
            AlpineComponent::make('uppy', __DIR__ . '/../resources/js/dist/components/uppy.js'),
        ], 'cloudmazing/filament-s3-multipart-upload');

        $this->app
            ->when(MultipartUploadController::class)
            ->needs(S3Client::class)
            ->give(function ($app) {
                return $app->make(FilesystemManager::class)->disk(config('filament-s3-multipart-upload.disk'))->getClient();
            });

        $this->app
            ->when(S3MultipartController::class)
            ->needs(S3Client::class)
            ->give(function ($app) {
                return $app->make(FilesystemManager::class)->disk(config('filament-s3-multipart-upload.disk'))->getClient();
            });


        $this->app
            ->when(TemporarySignedUrlController::class)
            ->needs(S3Client::class)
            ->give(function ($app) {
                return $app->make(FilesystemManager::class)->disk(config('filament-s3-multipart-upload.disk'))->getClient();
            });

        $this->app
            ->when(MultipartUploadCompletionController::class)
            ->needs(S3Client::class)
            ->give(function ($app) {
                return $app->make(FilesystemManager::class)->disk(config('filament-s3-multipart-upload.disk'))->getClient();
            });
    }
}

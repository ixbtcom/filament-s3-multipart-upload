<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
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
    }
}

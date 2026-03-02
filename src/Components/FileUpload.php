<?php

declare(strict_types=1);

namespace CloudMazing\FilamentS3MultipartUpload\Components;

use Closure;
use Filament\Forms\Components\Field;

class FileUpload extends Field
{
    protected string $view = 'filament-s3-multipart-upload::components.file-upload';

    protected int $maxFileSize = 5 * 1024 * 1024 * 1024; // 5GB

    protected int $maxNumberOfFiles = 10;

    protected bool $multiple = false;

    protected string $directory = '';

    protected Closure|bool $invisible = false;

    protected array $acceptedFileTypes = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(null);

        $this->dehydrateStateUsing(fn ($state) => $state ?: null);

        $this->afterStateHydrated(function (FileUpload $component, $state): void {
            if ($state === '' || $state === false) {
                $component->state(null);
            }
        });
    }

    public function invisible(Closure|bool $invisible = true): self
    {
        $this->invisible = $invisible;

        return $this;
    }

    public function getInvisible(): bool
    {
        return $this->evaluate($this->invisible);
    }

    public function directory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function acceptedFileTypes(array $types): self
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function getAcceptedFileTypes(): array
    {
        if (! empty($this->acceptedFileTypes)) {
            return $this->acceptedFileTypes;
        }

        return config('filament-s3-multipart-upload.accepted_mime_types', []);
    }

    public function hasAwsConfigured(): bool
    {
        $disk = config('filament-s3-multipart-upload.disk', 's3');

        return config("filesystems.disks.{$disk}.bucket")
            && config("filesystems.disks.{$disk}.key")
            && config("filesystems.disks.{$disk}.region")
            && config("filesystems.disks.{$disk}.secret");
    }

    public function companionUrl(): string
    {
        return '/'.config('filament-s3-multipart-upload.prefix', '_multipart-upload');
    }

    public function getPartSize(): int
    {
        return config('filament-s3-multipart-upload.part_size', 50 * 1024 * 1024);
    }

    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    public function maxFileSize(int $bytes): self
    {
        $this->maxFileSize = $bytes;

        return $this;
    }

    public function multiple(): self
    {
        $this->multiple = true;

        return $this;
    }

    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    public function maxNumberOfFiles(int $maxNumberOfFiles): self
    {
        $this->maxNumberOfFiles = $maxNumberOfFiles;

        return $this;
    }

    public function getMaxNumberOfFiles(): int
    {
        if (! $this->multiple) {
            return 1;
        }

        return $this->maxNumberOfFiles;
    }

    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return ($i === 0 ? (int) $size : number_format($size, 1)).' '.$units[$i];
    }
}

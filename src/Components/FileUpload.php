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
        return '/'.config('filament-s3-multipart-upload.prefix');
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
}

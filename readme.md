# Filament S3 Multipart Upload
A filament component that uses Uppy for multi-part uploads.

## Installation

This fork is maintained in [GitLab](https://glab.ixbt.site/packages/filament-s3-multipart-upload) on branch `master`.
To select it instead of the upstream package, configure this VCS repository in the consuming
application (GitLab access is required); the Composer package name remains `cloudmazing/filament-s3-multipart-upload`:

```bash
composer config repositories.filament-s3-multipart-upload vcs https://glab.ixbt.site/packages/filament-s3-multipart-upload.git
composer require cloudmazing/filament-s3-multipart-upload:dev-master
```

```sh
composer require cloudmazing/filament-s3-multipart-upload
```

```php
use CloudMazing\FilamentS3MultipartUpload\Components\FileUpload;

FileUpload::make('column_name')
    ->maxFileSize(10 * 1024 * 1024 * 1024)
    ->multiple()
    ->maxNumberOfFiles(5);
```

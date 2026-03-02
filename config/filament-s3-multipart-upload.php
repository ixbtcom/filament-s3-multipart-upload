<?php

declare(strict_types=1);

return [
    'prefix' => '_multipart-upload',

    'disk' => 's3',
    'expiry' => '+1 hour',

    // Size of each multipart chunk in bytes (default: 50MB)
    'part_size' => 50 * 1024 * 1024,
];

@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        @if($getInvisible()) style="display:none" @endif
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('uppy', 'cloudmazing/filament-s3-multipart-upload') }}"
        x-data="uppy({
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            maxFiles: @js($getMaxNumberOfFiles()),
            maxSize: @js($getMaxFileSize()),
            directory: @js($getDirectory()),
            companionUrl: @js($companionUrl()),
            csrfToken: @js(csrf_token()),
            acceptedTypes: @js($getAcceptedFileTypes()),
            partSize: @js($getPartSize()),
            disk: @js($getDisk()),
        })"
        wire:ignore
    >
        {{-- Drop Zone --}}
        <div
            x-on:dragover.prevent="isDragging = true"
            x-on:dragleave.prevent="isDragging = false"
            x-on:drop.prevent="handleDrop($event)"
            x-on:click="openFilePicker()"
            x-bind:class="{
                'border-primary-500 bg-primary-50 dark:bg-primary-900/20': isDragging,
                'border-gray-300 dark:border-white/10 hover:border-primary-400 dark:hover:border-primary-500': !isDragging && !uploadComplete,
                'border-success-300 dark:border-success-700': uploadComplete && !isUploading,
            }"
            class="relative flex flex-col items-center justify-center w-full rounded-lg border-2 border-dashed cursor-pointer transition-colors duration-200"
        >
            <input
                type="file"
                class="sr-only"
                @if($getAcceptedFileTypes()) accept="{{ implode(',', $getAcceptedFileTypes()) }}" @endif
                x-ref="fileInput"
                x-on:change="handleFileSelect($event)"
            />

            {{-- Empty State --}}
            <div x-show="!fileName && !isUploading" class="flex flex-col items-center gap-2 p-6 text-center">
                <div class="rounded-full bg-gray-100 dark:bg-white/5 p-3">
                    <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('Перетащите файл или нажмите для выбора') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Максимум') }} {{ $formatFileSize($getMaxFileSize()) }}
                    </p>
                </div>
            </div>

            {{-- Upload Progress --}}
            <div x-show="isUploading" x-cloak class="w-full p-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-gray-700 dark:text-gray-200 truncate pr-4" x-text="fileName"></span>
                    <span class="text-gray-500 dark:text-gray-400 tabular-nums shrink-0" x-text="progress + '%'"></span>
                </div>
                <div class="w-full h-2 bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-primary-500 rounded-full transition-all duration-300 ease-out"
                        x-bind:style="'width: ' + progress + '%'"
                    ></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Загрузка...') }}
                    <span x-show="fileSize" x-text="'(' + formatSize(fileSize) + ')'"></span>
                </p>
            </div>

            {{-- Uploaded File --}}
            <div x-show="fileName && !isUploading && uploadComplete" x-cloak class="w-full p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="rounded-full bg-success-50 dark:bg-success-500/10 p-2 shrink-0">
                            <svg class="h-5 w-5 text-success-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate" x-text="fileName"></p>
                            <p x-show="fileSize" class="text-xs text-gray-500 dark:text-gray-400" x-text="formatSize(fileSize)"></p>
                        </div>
                    </div>
                    @unless($isDisabled())
                        <button
                            type="button"
                            x-on:click.stop="removeFile()"
                            class="shrink-0 rounded-lg p-1.5 text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-500/10 transition-colors"
                            title="{{ __('Удалить') }}"
                        >
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endunless
                </div>
            </div>

            {{-- File selected but upload not started yet / waiting --}}
            <div x-show="fileName && !isUploading && !uploadComplete" x-cloak class="w-full p-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-gray-100 dark:bg-white/5 p-2 shrink-0">
                        <svg class="h-5 w-5 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate" x-text="fileName"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Подготовка...') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Message --}}
        <div
            x-show="errorMessage"
            x-cloak
            class="mt-2 rounded-lg bg-danger-50 dark:bg-danger-500/10 p-3"
        >
            <p class="text-sm text-danger-600 dark:text-danger-400" x-text="errorMessage"></p>
        </div>

        @unless($hasAwsConfigured())
            <div class="mt-2 rounded-lg bg-danger-50 dark:bg-danger-500/10 p-3">
                <p class="text-sm text-danger-600 dark:text-danger-400">
                    {{ __('S3 не настроен. Проверьте конфигурацию filesystems.') }}
                </p>
            </div>
        @endunless
    </div>
</x-dynamic-component>

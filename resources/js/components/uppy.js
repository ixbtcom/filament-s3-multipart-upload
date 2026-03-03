import Uppy from '@uppy/core'
import AwsS3Multipart from '@uppy/aws-s3-multipart'

export default function uppy({
    state,
    maxFiles,
    maxSize,
    directory,
    companionUrl,
    csrfToken,
    acceptedTypes,
    partSize,
    disk,
}) {
    return {
        state,
        uppy: null,

        // UI state
        fileName: null,
        fileSize: null,
        isDragging: false,
        isUploading: false,
        progress: 0,
        errorMessage: null,
        uploadComplete: false,

        init() {
            // If state already has a value, show it as uploaded
            if (this.state) {
                this.fileName = String(this.state).split('/').pop()
                this.uploadComplete = true
            }

            this.uppy = new Uppy({
                id: this.$el.id || 'uppy-' + Math.random().toString(36).substring(2, 11),
                autoProceed: true,
                restrictions: {
                    maxNumberOfFiles: maxFiles || 1,
                    maxFileSize: maxSize || null,
                    allowedFileTypes: acceptedTypes && acceptedTypes.length ? acceptedTypes : null,
                },
                onBeforeUpload: (files) => {
                    if (!directory) return files

                    const updated = {}
                    Object.keys(files).forEach((id) => {
                        updated[id] = {
                            ...files[id],
                            name: `${directory}/${files[id].name}`,
                        }
                    })
                    return updated
                },
            })

            const chunkSize = partSize || 50 * 1024 * 1024 // default 50MB

            this.uppy.use(AwsS3Multipart, {
                companionUrl: companionUrl,
                companionHeaders: {
                    'X-CSRF-TOKEN': csrfToken,
                    ...(disk ? { 'X-S3-Disk': disk } : {}),
                },
                getChunkSize: () => chunkSize,
                limit: 5,
                retryDelays: [0, 1000, 3000, 5000],
            })

            this.uppy.on('file-added', (file) => {
                this.fileName = file.name.split('/').pop()
                this.fileSize = file.size
                this.errorMessage = null
                this.uploadComplete = false
            })

            this.uppy.on('upload', () => {
                this.isUploading = true
                this.progress = 0
                this.errorMessage = null
            })

            this.uppy.on('progress', (percent) => {
                this.progress = percent
            })

            this.uppy.on('upload-success', (file, response) => {
                const key = file.meta?.key || response?.body?.path || null
                this.state = key
                this.uploadComplete = true
                this.isUploading = false
            })

            this.uppy.on('upload-error', (file, error) => {
                this.errorMessage = error?.message || 'Ошибка загрузки'
                this.isUploading = false
            })

            this.uppy.on('complete', (result) => {
                this.isUploading = false
                if (result.failed?.length > 0) {
                    this.errorMessage = 'Загрузка не удалась'
                }
            })

            this.uppy.on('restriction-failed', (file, error) => {
                this.errorMessage = error?.message || 'Файл не соответствует ограничениям'
            })

            this.uppy.on('error', (error) => {
                this.errorMessage = error?.message || 'Критическая ошибка'
                this.isUploading = false
            })
        },

        openFilePicker() {
            if (this.isUploading) return
            this.$refs.fileInput?.click()
        },

        handleFileSelect(event) {
            const file = event.target.files?.[0]
            if (!file) return

            this.addFile(file)
            event.target.value = ''
        },

        handleDrop(event) {
            this.isDragging = false
            const file = event.dataTransfer?.files?.[0]
            if (!file) return

            this.addFile(file)
        },

        addFile(file) {
            // Remove existing files (single file mode)
            this.uppy.getFiles().forEach((f) => this.uppy.removeFile(f.id))

            try {
                this.uppy.addFile({
                    name: file.name,
                    type: file.type,
                    data: file,
                    source: 'Local',
                })
            } catch (err) {
                this.errorMessage = err.message
            }
        },

        removeFile() {
            if (this.uppy) {
                this.uppy.cancelAll()
            }
            this.state = null
            this.fileName = null
            this.fileSize = null
            this.progress = 0
            this.isUploading = false
            this.uploadComplete = false
            this.errorMessage = null
        },

        formatSize(bytes) {
            if (!bytes) return ''
            const units = ['B', 'KB', 'MB', 'GB', 'TB']
            let i = 0
            let size = bytes
            while (size >= 1024 && i < units.length - 1) {
                size /= 1024
                i++
            }
            return size.toFixed(i === 0 ? 0 : 1) + ' ' + units[i]
        },

        destroy() {
            if (this.uppy) {
                this.uppy.cancelAll()
                this.uppy.destroy()
                this.uppy = null
            }
        },
    }
}

/**
 * Media Manager Alpine.js Component
 * Handles file uploads, deletions, and media grid interactions
 */

export function setupMediaManager() {
    return {
        uploading: false,
        error: null as string | null,

        async handleFileUpload(event: Event) {
            const input = event.target as HTMLInputElement;
            const files = input.files;

            if (!files || files.length === 0) return;

            this.uploading = true;
            this.error = null;

            const formData = new FormData();
            for (let file of files) {
                formData.append('files[]', file);
            }

            try {
                const response = await fetch(
                    (document.querySelector('[data-media-upload-url]') as HTMLElement)?.getAttribute('data-media-upload-url') || '/admin/media',
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content,
                        },
                        body: formData,
                    }
                );

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                // Reload page or update media grid
                window.location.reload();
            } catch (err) {
                this.error = err instanceof Error ? err.message : 'Upload failed';
            } finally {
                this.uploading = false;
                input.value = '';
            }
        },

        triggerFileInput() {
            const fileInput = document.getElementById('fileInput') as HTMLInputElement;
            if (fileInput) {
                fileInput.click();
            }
        },
    };
}

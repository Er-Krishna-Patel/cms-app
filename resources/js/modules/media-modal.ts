/**
 * Media Modal Component
 * Handles media selection and upload via Alpine.js
 */

export function setupMediaModal(modalId: string, purpose: string = 'insert') {
    return {
        activeModal: modalId,
        modalPurpose: purpose,
        mediaItems: [] as any[],
        loading: true,
        error: null as string | null,
        selectedMediaId: null as number | null,

        async init() {
            await this.loadMedia();
        },

        async loadMedia() {
            try {
                this.loading = true;
                this.error = null;

                const response = await fetch('/admin/media/api');
                if (!response.ok) throw new Error('Failed to load media');

                const data = await response.json();
                this.mediaItems = data.media || [];
            } catch (err) {
                this.error = err instanceof Error ? err.message : 'Failed to load media';
            } finally {
                this.loading = false;
            }
        },

        openModal() {
            const modal = document.getElementById(this.activeModal);
            if (modal) {
                modal.classList.add('active');
            }
        },

        closeModal() {
            const modal = document.getElementById(this.activeModal);
            if (modal) {
                modal.classList.remove('active');
            }
        },

        async uploadFile(event: Event) {
            const input = event.target as HTMLInputElement;
            const files = input.files;

            if (!files || files.length === 0) return;

            const formData = new FormData();
            for (let file of files) {
                formData.append('files[]', file);
            }

            try {
                const response = await fetch('/admin/media', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content,
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error('Upload failed');

                await this.loadMedia();
                input.value = '';
            } catch (err) {
                this.error = err instanceof Error ? err.message : 'Upload failed';
            }
        },

        selectMedia(mediaId: number, mediaUrl: string) {
            if (this.modalPurpose === 'featured') {
                // Set featured image
                const input = document.querySelector('input[name="featured_image_url"]') as HTMLInputElement;
                if (input) {
                    input.value = mediaUrl;
                    // Trigger preview update
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            } else if (this.modalPurpose === 'insert') {
                // Insert into TinyMCE
                if ((window as any).tinymce?.activeEditor) {
                    (window as any).tinymce.activeEditor.insertContent(
                        `<img src="${mediaUrl}" alt="Image" class="w-full h-auto">`
                    );
                }
            }

            this.closeModal();
        },
    };
}

/**
 * Post Editor Module
 * Handles post creation/editing functionality including:
 * - Auto-save drafts
 * - Featured image preview
 * - Slug generation
 * - Preview handling
 * - Form status management
 */

class PostEditor {
    constructor(options = {}) {
        this.options = {
            formSelector: 'form',
            titleSelector: 'input[name="title"]',
            slugInputSelector: '#slug-input',
            permalinkPreviewSelector: '#permalink-preview',
            statusInputSelector: '#form-status',
            contentSelector: 'textarea[name="content"]',
            previewBtnSelector: '#preview-btn',
            featuredInputSelector: '#featured-input',
            featuredPreviewSelector: '#featured-preview',
            featuredImgSelector: '#featured-img',
            autoSaveInterval: 30000, // 30 seconds
            autoSaveDraftUrl: null,
            ...options
        };

        this.form = document.querySelector(this.options.formSelector);
        this.title = document.querySelector(this.options.titleSelector);
        this.slugInput = document.querySelector(this.options.slugInputSelector);
        this.permalinkPreview = document.querySelector(this.options.permalinkPreviewSelector);
        this.statusInput = document.querySelector(this.options.statusInputSelector);
        this.contentField = document.querySelector(this.options.contentSelector);
        this.previewBtn = document.querySelector(this.options.previewBtnSelector);
        this.featuredInput = document.querySelector(this.options.featuredInputSelector);
        this.featuredPreview = document.querySelector(this.options.featuredPreviewSelector);
        this.featuredImg = document.querySelector(this.options.featuredImgSelector);

        this.slugEdited = false;
        this.autoSaveTimer = null;
        this.hasChanges = false;

        this.init();
    }

    init() {
        this.attachEventListeners();
        this.startAutoSave();
    }

    attachEventListeners() {
        // Title input for slug generation
        if (this.title) {
            this.title.addEventListener('input', () => this.onTitleChange());
        }

        // Slug input - mark as manually edited
        if (this.slugInput) {
            this.slugInput.addEventListener('input', () => {
                this.slugEdited = true;
                this.updatePermalinkPreview();
                this.markChanged();
            });
        }

        // Featured image preview
        if (this.featuredInput) {
            this.featuredInput.addEventListener('change', (e) => this.onFeaturedImageChange(e));
        }

        // Preview button
        if (this.previewBtn) {
            this.previewBtn.addEventListener('click', (e) => this.onPreviewClick(e));
        }

        // Form submission handlers
        if (this.form) {
            const submitButtons = this.form.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(btn => {
                btn.addEventListener('click', (e) => this.onSubmit(e, btn));
            });
        }

        // Mark changes on any form field
        if (this.form) {
            this.form.addEventListener('change', () => this.markChanged());
            this.form.addEventListener('keyup', () => this.markChanged());
        }
    }

    /**
     * Handle title change - auto-generate slug
     */
    onTitleChange() {
        this.markChanged();
        if (!this.slugEdited && this.title.value) {
            const newSlug = this.toSlug(this.title.value);
            if (this.slugInput) {
                this.slugInput.value = newSlug;
                this.updatePermalinkPreview();
            }
        }
    }

    /**
     * Convert string to URL-friendly slug
     */
    toSlug(str) {
        return str.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    /**
     * Update permalink preview
     */
    updatePermalinkPreview() {
        if (this.permalinkPreview && this.slugInput) {
            const slug = this.slugInput.value || 'post-slug';
            this.permalinkPreview.textContent = '/blog/' + slug;
        }
    }

    /**
     * Handle featured image file selection
     */
    onFeaturedImageChange(event) {
        const file = event.target.files[0];
        this.markChanged();
        
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (this.featuredImg) {
                    this.featuredImg.src = e.target.result;
                }
                if (this.featuredPreview) {
                    this.featuredPreview.classList.remove('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
    }

    /**
     * Handle preview button click
     * Allows preview even if content not filled
     */
    onPreviewClick(event) {
        event.preventDefault();

        if (!this.title || !this.title.value) {
            alert('Please add a title before previewing.');
            return;
        }

        // Save as draft first
        this.statusInput.value = 'draft';
        
        // Submit form
        this.form.submit();
    }

    /**
     * Handle form submission
     */
    onSubmit(event, button) {
        // Set status based on button clicked
        if (button.textContent.includes('Draft')) {
            this.statusInput.value = 'draft';
        } else if (button.textContent.includes('Publish')) {
            this.statusInput.value = 'published';
        }

        // Validate required fields only for publish
        if (this.statusInput.value === 'published') {
            if (!this.title || !this.title.value) {
                event.preventDefault();
                alert('Title is required to publish.');
                return;
            }
            if (!this.contentField || !this.contentField.value) {
                event.preventDefault();
                alert('Content is required to publish.');
                return;
            }
        }

        // Clear auto-save timer
        this.clearAutoSave();
    }

    /**
     * Mark form as having changes
     */
    markChanged() {
        this.hasChanges = true;
    }

    /**
     * Auto-save draft periodically
     */
    startAutoSave() {
        if (!this.options.autoSaveDraftUrl) {
            return;
        }

        this.autoSaveTimer = setInterval(() => {
            if (this.hasChanges && this.title && this.title.value) {
                this.saveDraftAuto();
            }
        }, this.options.autoSaveInterval);
    }

    /**
     * Save draft via AJAX without page reload
     */
    saveDraftAuto() {
        if (!this.form) return;

        const formData = new FormData(this.form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Ensure status is draft
        formData.set('status', 'draft');

        fetch(this.options.autoSaveDraftUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.hasChanges = false;
                // Show subtle notification
                this.showAutoSaveNotification();
            }
        })
        .catch(err => console.error('Auto-save failed:', err));
    }

    /**
     * Show auto-save notification
     */
    showAutoSaveNotification() {
        // Find or create notification element
        let notification = document.getElementById('auto-save-notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'auto-save-notification';
            notification.className = 'fixed bottom-4 right-4 bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm z-50';
            notification.textContent = 'Draft saved';
            document.body.appendChild(notification);
        }

        notification.classList.remove('hidden');
        
        // Hide after 2 seconds
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 2000);
    }

    /**
     * Clear auto-save timer
     */
    clearAutoSave() {
        if (this.autoSaveTimer) {
            clearInterval(this.autoSaveTimer);
            this.autoSaveTimer = null;
        }
    }

    /**
     * Destroy instance
     */
    destroy() {
        this.clearAutoSave();
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const autoSaveDraftUrl = document.querySelector('meta[name="auto-save-url"]')?.content || null;
    
    window.postEditor = new PostEditor({
        autoSaveDraftUrl: autoSaveDraftUrl
    });
});

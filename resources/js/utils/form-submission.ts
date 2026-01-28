/**
 * Form Submission Utility
 * Handles form submission with optional confirmation dialogs
 */

import { ConfirmationService } from './confirmation';

export class FormSubmissionHandler {
    /**
     * Setup delete form confirmation
     * Usage: data-confirm-delete="item type" on form
     */
    static setupDeleteForms() {
        document.addEventListener('submit', async (e: Event) => {
            const form = e.target as HTMLFormElement;
            if (!form.dataset.confirmDelete) return;

            e.preventDefault();

            const itemType = form.dataset.confirmDelete || 'item';
            const confirmed = await ConfirmationService.delete(itemType);

            if (confirmed) {
                form.submit();
            }
        });
    }

    /**
     * Setup generic form confirmation
     * Usage: data-confirm="message" on form
     */
    static setupConfirmationForms() {
        document.addEventListener('submit', async (e: Event) => {
            const form = e.target as HTMLFormElement;
            if (!form.dataset.confirm) return;

            e.preventDefault();

            const message = form.dataset.confirm || 'Are you sure?';
            const confirmed = await ConfirmationService.confirm(message);

            if (confirmed) {
                form.submit();
            }
        });
    }

    /**
     * Initialize all form handlers
     */
    static init() {
        this.setupDeleteForms();
        this.setupConfirmationForms();
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    FormSubmissionHandler.init();
});

export default FormSubmissionHandler;

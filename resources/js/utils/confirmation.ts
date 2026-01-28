/**
 * Confirmation Dialog Utility
 * Provides reusable confirmation dialogs using Alpine.js and native browser APIs
 */

export class ConfirmationService {
    static async confirm(message: string, title: string = 'Confirm Action'): Promise<boolean> {
        return new Promise((resolve) => {
            // Create modal element
            const modal = document.createElement('div');
            modal.className = 'confirmation-modal-overlay';
            modal.innerHTML = `
                <div class="confirmation-modal-container">
                    <div class="confirmation-modal-header">
                        <h2 class="confirmation-modal-title">${title}</h2>
                    </div>
                    <div class="confirmation-modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="confirmation-modal-footer">
                        <button class="btn-cancel" type="button">Cancel</button>
                        <button class="btn-confirm" type="button">Confirm</button>
                    </div>
                </div>
            `;

            // Add styles if not already present
            if (!document.getElementById('confirmation-modal-styles')) {
                const style = document.createElement('style');
                style.id = 'confirmation-modal-styles';
                style.textContent = `
                    .confirmation-modal-overlay {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background-color: rgba(0, 0, 0, 0.5);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 10000;
                    }

                    .confirmation-modal-container {
                        background: white;
                        border-radius: 0.5rem;
                        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                        min-width: 300px;
                        max-width: 500px;
                    }

                    .confirmation-modal-header {
                        padding: 1.5rem;
                        border-bottom: 1px solid #e5e7eb;
                    }

                    .confirmation-modal-title {
                        font-size: 1.125rem;
                        font-weight: 600;
                        color: #111827;
                        margin: 0;
                    }

                    .confirmation-modal-body {
                        padding: 1.5rem;
                    }

                    .confirmation-modal-body p {
                        color: #374151;
                        margin: 0;
                    }

                    .confirmation-modal-footer {
                        display: flex;
                        justify-content: flex-end;
                        gap: 0.75rem;
                        padding: 1.5rem;
                        border-top: 1px solid #e5e7eb;
                    }

                    .btn-cancel,
                    .btn-confirm {
                        padding: 0.5rem 1rem;
                        border: none;
                        border-radius: 0.375rem;
                        font-size: 0.875rem;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    }

                    .btn-cancel {
                        background-color: #e5e7eb;
                        color: #111827;
                    }

                    .btn-cancel:hover {
                        background-color: #d1d5db;
                    }

                    .btn-confirm {
                        background-color: #dc2626;
                        color: white;
                    }

                    .btn-confirm:hover {
                        background-color: #b91c1c;
                    }
                `;
                document.head.appendChild(style);
            }

            // Attach event listeners
            const confirmBtn = modal.querySelector('.btn-confirm') as HTMLButtonElement;
            const cancelBtn = modal.querySelector('.btn-cancel') as HTMLButtonElement;

            confirmBtn.addEventListener('click', () => {
                modal.remove();
                resolve(true);
            });

            cancelBtn.addEventListener('click', () => {
                modal.remove();
                resolve(false);
            });

            // Allow escape key to cancel
            const handleEscape = (e: KeyboardEvent) => {
                if (e.key === 'Escape') {
                    modal.remove();
                    document.removeEventListener('keydown', handleEscape);
                    resolve(false);
                }
            };
            document.addEventListener('keydown', handleEscape);

            // Add modal to page
            document.body.appendChild(modal);
        });
    }

    static async delete(itemName: string = 'item'): Promise<boolean> {
        return this.confirm(
            `Are you sure you want to delete this ${itemName}? This action cannot be undone.`,
            'Delete Confirmation'
        );
    }
}

// Export for use in inline scripts
declare global {
    interface Window {
        ConfirmationService: typeof ConfirmationService;
    }
}

window.ConfirmationService = ConfirmationService;

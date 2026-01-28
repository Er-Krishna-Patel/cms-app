import './bootstrap';
import './media-upload';

import Alpine from 'alpinejs';
import { ConfirmationService } from './utils/confirmation';
import { FormSubmissionHandler } from './utils/form-submission';

window.Alpine = Alpine;
window.ConfirmationService = ConfirmationService;
window.FormSubmissionHandler = FormSubmissionHandler;

Alpine.start();

// Initialize form submission handlers
FormSubmissionHandler.init();

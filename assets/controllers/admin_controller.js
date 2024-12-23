import { Controller } from '@hotwired/stimulus';
import * as Turbo from '@hotwired/turbo';

export default class extends Controller {
    connect() {
        // Ensure we run this after DOM is fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeAdmin());
        } else {
            this.initializeAdmin();
        }
    }

    initializeAdmin() {
        // Check if we're in admin area
        if (window.location.pathname.startsWith('/admin')) {
            // Disable Turbo globally for admin routes
            Turbo.session.drive = false;
            
            // Ensure all admin links have Turbo disabled
            this.disableTurboOnElements('a[href^="/admin"]');

            // Ensure forms in admin area have Turbo disabled
            this.disableTurboOnElements('form', (form) => form.action.includes('/admin'));

            // Clean up any duplicate content that might have been created
            this.cleanupDuplicateContent();

            // Add mutation observer to handle dynamically added elements
            this.observeDOMChanges();
        }
    }

    disableTurboOnElements(selector, filterFn = null) {
        document.querySelectorAll(selector).forEach(element => {
            if (!filterFn || filterFn(element)) {
                element.setAttribute('data-turbo', 'false');
            }
        });
    }

    cleanupDuplicateContent() {
        const adminWrappers = document.querySelectorAll('#admin-wrapper');
        if (adminWrappers.length > 1) {
            // Keep only the first wrapper
            for (let i = 1; i < adminWrappers.length; i++) {
                adminWrappers[i].remove();
            }
        }
    }

    observeDOMChanges() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    // Check for newly added admin links or forms
                    this.disableTurboOnElements('a[href^="/admin"]');
                    this.disableTurboOnElements('form', (form) => form.action.includes('/admin'));
                    
                    // Check for duplicate admin wrappers
                    this.cleanupDuplicateContent();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
} 
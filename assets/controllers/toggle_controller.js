import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content', 'trigger']

    connect() {
        // Get the initial state from the trigger element
        const triggerElement = this.element.querySelector('[data-action*="toggle#toggle"]');
        if (triggerElement) {
            this.toggleContent(triggerElement.checked);
        }
    }

    toggle(event) {
        this.toggleContent(event.target.checked);
    }

    toggleContent(isChecked) {
        this.contentTarget.classList.toggle('hidden', !isChecked);
    }
} 
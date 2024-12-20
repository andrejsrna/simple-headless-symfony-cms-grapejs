import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];

    connect() {
        // Initial state is handled by the template
    }

    toggle(event) {
        if (event.target.checked) {
            this.contentTarget.classList.remove('hidden');
        } else {
            this.contentTarget.classList.add('hidden');
        }
    }
} 
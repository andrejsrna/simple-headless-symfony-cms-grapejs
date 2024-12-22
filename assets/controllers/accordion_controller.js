import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content', 'icon', 'trigger']

    connect() {
        // Initialize the accordion state
        this.isOpen = false;
    }

    toggle() {
        this.isOpen = !this.isOpen;
        
        // Toggle content visibility
        this.contentTarget.classList.toggle('hidden');
        
        // Rotate the icon
        if (this.isOpen) {
            this.iconTarget.classList.add('rotate-180');
        } else {
            this.iconTarget.classList.remove('rotate-180');
        }
    }
} 
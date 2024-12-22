import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['source', 'output']

    connect() {
        if (!this.hasSourceTarget || !this.hasOutputTarget) return;
        
        // Generate initial slug if source has value and output is empty
        if (this.sourceTarget.value && !this.outputTarget.value) {
            this.generateSlug();
        }
    }

    generateSlug() {
        const value = this.sourceTarget.value;
        if (!value) return;

        const slug = value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');

        this.outputTarget.value = slug;
    }
} 
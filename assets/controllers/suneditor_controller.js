import { Controller } from '@hotwired/stimulus';
import suneditor from 'suneditor';
import plugins from 'suneditor/src/plugins';

export default class extends Controller {
    static instances = new Set();

    initialize() {
        // Store the instance ID
        this.instanceId = `suneditor_${Math.random().toString(36).substr(2, 9)}`;
    }

    connect() {
        // Check if this instance is already tracked
        if (this.constructor.instances.has(this.instanceId)) {
            return;
        }

        // Track this instance
        this.constructor.instances.add(this.instanceId);

        // Ensure old instances are cleaned up
        if (this.element.suneditor && typeof this.element.suneditor.destroy === 'function') {
            this.element.suneditor.destroy();
            this.element.suneditor = null;
        }

        // Set unique ID for the element
        this.element.id = this.instanceId;

        // Initialize editor
        this.editor = suneditor.create(this.element, {
            plugins: plugins,
            buttonList: [
                ['undo', 'redo'],
                ['font', 'fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
                ['removeFormat'],
                ['fontColor', 'hiliteColor'],
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'table'],
                ['link', 'image'],
                ['fullScreen', 'showBlocks', 'codeView'],
            ],
            height: '400px',
            width: '100%',
            defaultStyle: 'font-family: inherit; font-size: 1rem; line-height: 1.5;',
            onChange: (contents) => {
                // Update the hidden textarea
                this.element.value = contents;
                // Dispatch input event to trigger form validation
                this.element.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        // Store reference to editor on the element
        this.element.suneditor = this.editor;

        // Set initial content if exists
        if (this.element.value) {
            this.editor.setContents(this.element.value);
        }

        // Handle form submission
        const form = this.element.closest('form');
        if (form) {
            this.submitHandler = (event) => {
                if (this.editor && typeof this.editor.getContents === 'function') {
                    try {
                        // Get the contents before form submission
                        const contents = this.editor.getContents();
                        // Update the textarea value
                        this.element.value = contents;
                        console.log('Form submitted with content:', contents);
                    } catch (error) {
                        console.warn('Error getting editor contents:', error);
                    }
                }
            };
            form.addEventListener('submit', this.submitHandler);
        }

        // Handle Turbo cache
        document.addEventListener('turbo:before-cache', this.cleanup);
    }

    cleanup = () => {
        try {
            if (this.editor && typeof this.editor.destroy === 'function') {
                this.editor.destroy();
            }
        } catch (error) {
            console.warn('Error destroying Suneditor:', error);
        }
        
        this.editor = null;
        this.element.suneditor = null;
        this.constructor.instances.delete(this.instanceId);
    }

    disconnect() {
        // Remove form submit handler if it exists
        const form = this.element.closest('form');
        if (form && this.submitHandler) {
            form.removeEventListener('submit', this.submitHandler);
        }

        // Remove Turbo cache handler
        document.removeEventListener('turbo:before-cache', this.cleanup);

        // Cleanup editor
        this.cleanup();
    }
} 
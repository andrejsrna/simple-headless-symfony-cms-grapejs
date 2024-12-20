import { Controller } from '@hotwired/stimulus';
import suneditor from 'suneditor';
import plugins from 'suneditor/src/plugins';

export default class extends Controller {
    connect() {
        // Check if editor is already initialized
        if (this.editor) {
            return;
        }

        // Generate a unique ID for the editor
        const uniqueId = `suneditor_${Math.random().toString(36).substr(2, 9)}`;
        this.element.id = uniqueId;

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
                console.log('Editor content changed:', contents);
                this.element.value = contents;
                // Dispatch input event to trigger form validation
                this.element.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        // Set initial content if exists
        if (this.element.value) {
            console.log('Setting initial content:', this.element.value);
            this.editor.setContents(this.element.value);
        }

        // Handle form submission
        const form = this.element.closest('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                console.log('Form submitting, getting editor contents');
                const contents = this.editor.getContents();
                console.log('Editor contents:', contents);
                this.element.value = contents;
            });
        }
    }

    disconnect() {
        if (this.editor) {
            this.editor.destroy();
            this.editor = null;
        }
    }
} 
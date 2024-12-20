import { Controller } from '@hotwired/stimulus';
import suneditor from 'suneditor';
import plugins from 'suneditor/src/plugins';

export default class extends Controller {
    connect() {
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
                this.element.value = contents;
            }
        });

        // Set initial content if exists
        if (this.element.value) {
            this.editor.setContents(this.element.value);
        }

        // Handle form submission
        this.element.closest('form')?.addEventListener('submit', () => {
            this.element.value = this.editor.getContents();
        });
    }

    disconnect() {
        this.editor.destroy();
    }
} 
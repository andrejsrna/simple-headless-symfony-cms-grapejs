import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'preview', 'placeholder', 'filename'];

    connect() {
        this.bindEvents();
    }

    bindEvents() {
        this.element.addEventListener('dragover', this.handleDragOver.bind(this));
        this.element.addEventListener('dragleave', this.handleDragLeave.bind(this));
        this.element.addEventListener('drop', this.handleDrop.bind(this));
    }

    handleDragOver(e) {
        e.preventDefault();
        this.element.classList.add('border-blue-500', 'bg-blue-50');
    }

    handleDragLeave(e) {
        e.preventDefault();
        this.element.classList.remove('border-blue-500', 'bg-blue-50');
    }

    handleDrop(e) {
        e.preventDefault();
        this.element.classList.remove('border-blue-500', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        if (files.length) {
            this.inputTarget.files = files;
            this.previewImage(files[0]);
        }
    }

    preview(event) {
        const file = event.target.files[0];
        if (file) {
            this.previewImage(file);
        }
    }

    previewImage(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = (e) => {
                this.previewTarget.src = e.target.result;
                this.previewTarget.classList.remove('hidden');
                this.placeholderTarget.classList.add('hidden');
                this.filenameTarget.textContent = file.name;
            };
            
            reader.readAsDataURL(file);
        }
    }

    browse() {
        this.inputTarget.click();
    }
} 
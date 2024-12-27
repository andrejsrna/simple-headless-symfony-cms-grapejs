import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['modal', 'grid', 'uploadForm', 'fileInput'];

    connect() {
        this.loadImages();
        this.bindEvents();
    }

    bindEvents() {
        // Handle file input change
        if (this.hasFileInputTarget) {
            this.fileInputTarget.addEventListener('change', this.handleFileChange.bind(this));
        }
    }

    async loadImages() {
        try {
            const response = await fetch('/admin/images/list');
            const data = await response.json();
            
            if (data.files) {
                this.renderImages(data.files);
            }
        } catch (error) {
            console.error('Error loading images:', error);
        }
    }

    renderImages(images) {
        if (!this.hasGridTarget) return;

        this.gridTarget.innerHTML = images.map(image => `
            <div class="group relative bg-white dark:bg-gray-800 rounded-lg border shadow-sm overflow-hidden cursor-pointer"
                 data-action="click->image-gallery#selectImage"
                 data-url="${image.url}"
                 data-alt="${image.alt || image.key}">
                <div class="aspect-square w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
                    <img src="${image.url}" 
                         alt="${image.alt || image.key}"
                         class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                </div>
                <div class="p-2">
                    <p class="text-sm truncate">${image.key}</p>
                </div>
            </div>
        `).join('');
    }

    selectImage(event) {
        const target = event.currentTarget;
        const url = target.dataset.url;
        const alt = target.dataset.alt;

        // Find the Sundance editor controller and insert the image
        const editorController = this.application.getControllerForElementAndIdentifier(
            document.querySelector('[data-controller="suneditor"]'),
            'suneditor'
        );

        if (editorController) {
            editorController.insertImage(url, alt);
        }
    }

    async handleFileChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch('/admin/images/upload', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                // Reload images after successful upload
                this.loadImages();
                // Reset file input
                this.fileInputTarget.value = '';
            } else {
                console.error('Upload failed');
            }
        } catch (error) {
            console.error('Error uploading file:', error);
        }
    }

    openModal() {
        if (this.hasModalTarget) {
            this.modalTarget.showModal();
        }
    }

    closeModal() {
        if (this.hasModalTarget) {
            this.modalTarget.close();
        }
    }
} 
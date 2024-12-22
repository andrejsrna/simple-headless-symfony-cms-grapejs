import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['suneditor', 'grapejs']

    startGrapeJS(event) {
        const pageId = event.currentTarget.dataset.pageId;
        window.location.href = `/editor/page/${pageId}`;
    }
} 
import { Controller } from '@hotwired/stimulus';
import grapesjs from 'grapesjs';
import plugin from 'grapesjs-preset-webpage';

// Import GrapesJS styles
import 'grapesjs/dist/css/grapes.min.css';

export default class extends Controller {
    static values = {
        pageId: Number,
        pageSlug: String,
        content: String
    }

    connect() {
        console.log('GrapeJS controller connected');
        this.initGrapeJS();
    }

    initGrapeJS() {
        const editor = grapesjs.init({
            container: '#gjs',
            height: '100vh',
            fromElement: true,
            storageManager: false,
            plugins: [plugin],
            pluginsOpts: {
                [plugin]: { /* options */ }
            },
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'
                ]
            },
            blockManager: {
                blocks: [
                    // Typography
                    {
                        id: 'h1',
                        label: 'H1 Heading',
                        category: 'Typography',
                        content: '<h1 class="text-5xl font-bold mb-6">Large Heading</h1>',
                        activate: true
                    },
                    {
                        id: 'h2',
                        label: 'H2 Heading',
                        category: 'Typography',
                        content: '<h2 class="text-4xl font-bold mb-4">Medium Heading</h2>'
                    },
                    {
                        id: 'h3',
                        label: 'H3 Heading',
                        category: 'Typography',
                        content: '<h3 class="text-3xl font-semibold mb-4">Small Heading</h3>'
                    },
                    {
                        id: 'paragraph',
                        label: 'Paragraph',
                        category: 'Typography',
                        content: '<p class="text-base leading-relaxed mb-4">Add your text here. This is a paragraph block that you can use for regular content.</p>'
                    },
                    {
                        id: 'text-block',
                        label: 'Text Block',
                        category: 'Typography',
                        content: '<div class="prose max-w-none"><p>Add your text here. This block supports rich text editing.</p></div>'
                    },
                    {
                        id: 'quote',
                        label: 'Quote',
                        category: 'Typography',
                        content: '<blockquote class="pl-4 border-l-4 border-gray-300 italic my-4">Add a meaningful quote here</blockquote>'
                    },

                    // Basic Elements
                    {
                        id: 'image',
                        label: 'Image',
                        category: 'Basic',
                        select: true,
                        content: { type: 'image' },
                        activate: true
                    },
                    {
                        id: 'link',
                        label: 'Link',
                        category: 'Basic',
                        content: '<a href="#" class="text-blue-600 hover:text-blue-800 underline">Link Text</a>'
                    },
                    {
                        id: 'button',
                        label: 'Button',
                        category: 'Basic',
                        content: '<button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-colors">Button</button>'
                    },
                    {
                        id: 'divider',
                        label: 'Divider',
                        category: 'Basic',
                        content: '<hr class="my-8 border-t border-gray-300">'
                    },

                    // Layout Blocks
                    {
                        id: 'container',
                        label: 'Container',
                        category: 'Layout',
                        content: '<div class="container mx-auto px-4"></div>'
                    },
                    {
                        id: 'section',
                        label: 'Section',
                        category: 'Layout',
                        content: `
                            <section class="py-12 px-4">
                                <div class="container mx-auto">
                                    <h2 class="text-3xl font-bold mb-6">Section Title</h2>
                                    <p class="text-lg text-gray-700">Add your content here</p>
                                </div>
                            </section>
                        `
                    },
                    {
                        id: '2-columns',
                        label: '2 Columns',
                        category: 'Layout',
                        content: `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-4">Column 1</h3>
                                    <p>Add content for the first column here.</p>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-4">Column 2</h3>
                                    <p>Add content for the second column here.</p>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: '3-columns',
                        label: '3 Columns',
                        category: 'Layout',
                        content: `
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-4">Column 1</h3>
                                    <p>Add content for the first column here.</p>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-4">Column 2</h3>
                                    <p>Add content for the second column here.</p>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-4">Column 3</h3>
                                    <p>Add content for the third column here.</p>
                                </div>
                            </div>
                        `
                    },

                    // Components
                    {
                        id: 'card',
                        label: 'Card',
                        category: 'Components',
                        content: `
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <img src="https://via.placeholder.com/400x200" alt="Card image" class="w-full h-48 object-cover">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">Card Title</h3>
                                    <p class="text-gray-600 mb-4">Card description goes here. Add your content.</p>
                                    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Learn More</button>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'feature',
                        label: 'Feature',
                        category: 'Components',
                        content: `
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold mb-2">Feature Title</h3>
                                    <p class="text-gray-600">Describe the feature here. Add your content.</p>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'testimonial',
                        label: 'Testimonial',
                        category: 'Components',
                        content: `
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <div class="flex items-center mb-4">
                                    <img src="https://via.placeholder.com/60x60" alt="Avatar" class="w-12 h-12 rounded-full">
                                    <div class="ml-4">
                                        <h4 class="font-semibold">John Doe</h4>
                                        <p class="text-gray-600 text-sm">CEO, Company Name</p>
                                    </div>
                                </div>
                                <p class="text-gray-700 italic">"Add your testimonial text here. Make it compelling and authentic."</p>
                            </div>
                        `
                    },
                    {
                        id: 'cta',
                        label: 'Call to Action',
                        category: 'Components',
                        content: `
                            <div class="bg-blue-600 text-white py-12 px-4">
                                <div class="container mx-auto text-center">
                                    <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
                                    <p class="text-xl mb-8">Join thousands of satisfied customers today.</p>
                                    <button class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                                        Get Started
                                    </button>
                                </div>
                            </div>
                        `
                    }
                ]
            }
        });

        // Add preview command
        editor.Commands.add('preview-page', {
            run: (editor) => {
                // Open the page in a new tab using the slug
                window.open(`/page/${this.pageSlugValue}`, '_blank');
            }
        });

        // Add save command
        editor.Commands.add('save-page', {
            run: async (editor) => {
                try {
                    const html = editor.getHtml();
                    const css = editor.getCss();
                    const content = html; // You might want to combine html and css if needed

                    const response = await fetch(`/admin/pages/${this.pageIdValue}/content`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ content })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to save content');
                    }

                    editor.Notifications.add({
                        type: 'success',
                        title: 'Success',
                        content: 'Content saved successfully',
                        timeout: 3000
                    });
                } catch (error) {
                    console.error('Save error:', error);
                    editor.Notifications.add({
                        type: 'error',
                        title: 'Error',
                        content: error.message || 'Failed to save content',
                        timeout: 3000
                    });
                }
            }
        });

        // Add keyboard shortcut for save
        editor.Commands.add('core:save', {
            run: (editor) => editor.runCommand('save-page')
        });

        // Add buttons to the top bar
        editor.Panels.addButton('options', [
            {
                id: 'save-btn',
                label: 'Save',
                command: 'save-page',
                attributes: { title: 'Save Changes' }
            },
            {
                id: 'preview-btn',
                label: '👁 Preview',
                command: 'preview-page',
                attributes: { title: 'Preview Page' }
            }
        ]);

        // Store editor instance
        this.editor = editor;

        // Add autosave warning when leaving page
        window.onbeforeunload = (e) => {
            if (editor.getDirtyCount() > 0) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        };
    }

    disconnect() {
        if (this.editor) {
            window.onbeforeunload = null;
            this.editor.destroy();
        }
    }
} 
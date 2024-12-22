import './bootstrap.js';
import '@hotwired/turbo';

// Import other dependencies after Turbo
import './controllers';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Import Stimulus
import { startStimulusApp } from '@symfony/stimulus-bridge';

// Register Stimulus controllers
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

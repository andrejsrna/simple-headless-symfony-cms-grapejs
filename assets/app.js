import './bootstrap.js';
import { Application } from '@hotwired/stimulus';
import * as Turbo from '@hotwired/turbo';

// Import dependencies
import './controllers';
import './styles/app.css';

// Import Stimulus
import { startStimulusApp } from '@symfony/stimulus-bridge';

// Register Stimulus controllers
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

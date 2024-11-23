import 'unfonts.css';
import './bootstrap.ts';

import { createPinia } from 'pinia';

// create single pinia instance across all components
const pinia = createPinia();

document.addEventListener('vue:before-mount', (event) => {
  const {
    componentName, // The Vue component's name
    component, // The resolved Vue component
    props, // The props that will be injected to the component
    app, // The Vue application instance
  } = event.detail;

  app.use(pinia);
});

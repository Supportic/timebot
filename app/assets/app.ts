// @ts-nocheck

import 'unfonts.css';
import './bootstrap.ts';

import { createPinia } from 'pinia';
import App from '@/vue/App.vue';

// create single pinia instance across all components
const pinia = createPinia();

document.addEventListener('vue:before-mount', (event) => {
  const {
    componentName, // The Vue component's name
    component, // The resolved Vue component
    props, // The props that will be injected to the component
    app, // The Vue application instance
  }: {
    componentName: string; // The Vue component's name
    component; // The resolved Vue component
    props; // The props that will be injected to the component
    app: typeof App; // The Vue application instance
  } = event.detail;

  // app.component('Component', Component)

  app.use(pinia);
});

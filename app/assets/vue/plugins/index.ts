/**
 * plugins/index.ts
 *
 * Automatically included in `./src/index.ts`
 */

// Plugins
// import vuetify from './vuetify';
import './vuetify';
import { PrimeVue, Aura } from './primevue';

// Types
import type { App } from 'vue';

const registerPlugins = (app: App) => {
  // app.use(vuetify);
  app.use(PrimeVue, { theme: { preset: Aura } });
};

export { registerPlugins };

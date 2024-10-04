/**
 * plugins/index.ts
 *
 * Automatically included in `./src/index.ts`
 */

// Plugins
// import vuetify from './vuetify';
import './vuetify';

// Types
import type { App } from 'vue';

const registerPlugins = (app: App) => {
  // app.use(vuetify);
  app.version
};

export { registerPlugins };

import {
  startStimulusApp,
  registerControllers,
} from 'vite-plugin-symfony/stimulus/helpers';

import {
  type VueModule,
  registerVueControllerComponents,
} from 'vite-plugin-symfony/stimulus/helpers/vue';

// register Vue components before startStimulusApp
registerVueControllerComponents(
  import.meta.glob<VueModule>('./vue/controllers/**/*.vue')
);

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

// with vite
registerControllers(
  app,
  import.meta.glob<StimulusControllerInfosImport>(
    './controllers/*_controller.ts',
    {
      query: '?stimulus',
      eager: true,
    }
  )
);

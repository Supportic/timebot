import { startStimulusApp, registerControllers } from 'vite-plugin-symfony/stimulus/helpers';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

// with vite
registerControllers(
  app,
  import.meta.glob('./controllers/*_(lazy)?controller.[jt]s(x)?')
);
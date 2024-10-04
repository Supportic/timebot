import { defineConfig } from 'vite';
import { resolve } from 'node:path';
import { fileURLToPath, URL } from 'node:url';
import vue from '@vitejs/plugin-vue';
import symfonyPlugin from 'vite-plugin-symfony';
import Components from 'unplugin-vue-components/vite';
import Unfonts from 'unplugin-fonts/vite';

const pathResolve = (dir: string) => {
  return resolve(__dirname, dir);
};

const fontFamilies = [
  {
    /**
     * Name of the font family.
     */
    name: 'Montserrat',
    /**
     * Local name of the font. Used to add `src: local()` to `@font-rule`.
     */
    local: 'MontserratVariable',
    /**
     * Regex(es) of font files to import. The names of the files will
     * predicate the `font-style` and `font-weight` values of the `@font-rule`'s.
     */
    src: './assets/fonts/MontserratVariable.woff2',
  },
  {
    name: 'MontserratItalic',
    local: 'MontserratVariable-italic',
    src: './assets/fonts/MontserratVariable-italic.woff2',
  },
  {
    name: 'Inter',
    local: 'InterVariable',
    src: './assets/fonts/InterVariable.woff2',
  },
  {
    name: 'InterItalic',
    local: 'InterVariable-italic',
    src: './assets/fonts/InterVariable-italic.woff2',
  },
];

export default defineConfig({
  plugins: [
    vue(),
    Components({}),
    Unfonts({
      custom: {
        families: fontFamilies,

        /**
         * Defines the default `font-display` value used for the generated
         * `@font-rule` classes.
         */
        display: 'auto',

        /**
         * Using `<link rel="preload">` will trigger a request for the WebFont
         * early in the critical rendering path, without having to wait for the
         * CSSOM to be created.
         */
        preload: true,

        /**
         * Using `<link rel="prefetch">` is intended for prefetching resources
         * that will be used in the next navigation/page load
         * (e.g. when you go to the next page)
         *
         * Note: this can not be used with `preload`
         */
        prefetch: true,

        /**
         * define where the font load tags should be inserted
         * default: 'head-prepend'
         *   values: 'head' | 'body' | 'head-prepend' | 'body-prepend'
         */
        injectTo: 'head-prepend',
      },
    }),
    symfonyPlugin({
      /**
       * or define custom path for your controllers.json
       * stimulus: './assets/other-dir/controllers.json
       */
      stimulus: true,

      // as we set `server.host` to 0.0.0.0
      // we must explicitly set the server host name
      viteDevServerHostname: 'localhost',
    }),
  ],
  publicDir: false,
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./assets', import.meta.url)),
      '@components': pathResolve('./assets/vue/components'),
      '@styles': pathResolve('./assets/styles'),
      '@fonts': pathResolve('./assets/fonts'),
      '@types': pathResolve('./assets/scripts/types'),
      '@interfaces': pathResolve('./assets/scripts/interfaces'),
    },
    extensions: ['.js', '.json', '.jsx', '.mjs', '.ts', '.tsx', '.vue'],
  },
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
      },
    },
  },
  build: {
    assetsInlineLimit: 512,
    manifest: true,
    rollupOptions: {
      input: {
        app: './assets/app.ts',
        theme: './assets/styles/theme.scss',
      },
      output: {
        manualChunks: {
          vue: ['vue'],
        },
      },
    },
  },
  server: {
    // Required to listen on all interfaces
    host: '0.0.0.0',
    port: 3000,
    // hmr: {
    //     protocol: "ws"
    // }
    watch: {
      ignored: ['**/.idea/**', '**/tests/**', '**/var/**', '**/vendor/**'],
    },
  },
  preview: {
    host: '0.0.0.0',
    port: 3005,
  },
});

import { defineConfig, UserConfig } from 'vite';
import Vue from '@vitejs/plugin-vue';
import symfonyPlugin from 'vite-plugin-symfony';
import Components from 'unplugin-vue-components/vite';
import Icons from 'unplugin-icons/vite';
import IconsResolver from 'unplugin-icons/resolver';
import { PrimeVueResolver } from '@primevue/auto-import-resolver';
import Unfonts from 'unplugin-fonts/vite';
import tsconfigPaths from 'vite-tsconfig-paths';
import AutoImport from 'unplugin-auto-import/vite';
import tailwindcss from '@tailwindcss/vite';
import StimulusHMR from 'vite-plugin-stimulus-hmr';

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

export default defineConfig((config: UserConfig): UserConfig => {
  // const isProduction = mode === 'production';
  // const isDevelopment = command === 'serve';

  return {
    plugins: [
      Vue(),
      StimulusHMR(),
      // make lib functions globally available to use by saving named libs into auto-imports.d.ts
      AutoImport({
        dts: './assets/auto-imports.d.ts', // enable typescript support
        imports: ['vue', '@vueuse/core', 'pinia'],
      }),
      Icons({ compiler: 'vue3', scale: 1 }),
      Components({
        dts: './assets/icons.d.ts', // enable typescript support (create on build)
        resolvers: [
          IconsResolver({
            prefix: 'icon',
          }),
          // https://primevue.org/autoimport/
          PrimeVueResolver(),
        ],
      }),
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
        stimulus: true,
        /**
         * or define custom path for your controllers.json
         * stimulus: './assets/other-dir/controllers.json
         */
        // stimulus: {
        //   controllersDir: './assets/controllers',
        //   controllersFilePath: './assets/controllers.json',
        // },

        // as we set `server.host` to 0.0.0.0
        // we must explicitly set the server host name
        viteDevServerHostname: 'localhost',
      }),
      tsconfigPaths(),
      tailwindcss(),
    ],
    publicDir: 'public',
    base: '/build',
    build: {
      assetsInlineLimit: 512,
      outDir: 'public/build',
      manifest: true,
      emptyOutDir: true,
      // minify: false,
      rollupOptions: {
        input: {
          login_style: './assets/styles/pages/login.css',
          login: './assets/scripts/pages/login.ts',

          // vue: './assets/entrypoints/vue.ts',

          // backend JS
          app: './assets/entrypoints/app.ts',
          // backend CSS
          global: './assets/styles/global.css',
        },
        output: {
          entryFileNames: `assets/[name].[hash:8].js`,
          chunkFileNames: `assets/[name].[hash:8].js`,
          assetFileNames: `assets/[name].[hash:8].[ext]`,
          compact: true,
          manualChunks: {
            vue: ['vue', 'pinia'],
          },
          // manualChunks: (id: string) => {
          //   if (id.includes('node_modules')) {
          //     if (
          //       id.includes('vue') ||
          //       id.includes('pinia') ||
          //       id.includes('vite-plugin-symfony')
          //     )
          //       return 'vue';
          //     return 'vendor';
          //   }
          // },
        },
      },
    },
    optimizeDeps: {
      exclude: ['vue'],
    },
    server: {
      // Required to listen on all interfaces
      host: '0.0.0.0',
      port: 3000,
      hmr: {
        // Allow accessing from other devices on the network
        host: process.env.VITE_HMR_HOST || '192.168.178.37',
        protocol: 'ws',
        port: parseInt(process.env.VITE_HMR_PORT || '3000'),
      },
      cors: true,
      fs: {
        allow: ['.'],
      },
      watch: {
        // usePolling: true,
        ignored: ['**/.idea/**', '**/tests/**', '**/var/**', '**/vendor/**'],
      },
    },
    preview: {
      host: '0.0.0.0',
      port: 3005,
    },
  };
});

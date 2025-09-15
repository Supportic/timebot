import { defineConfig, ConfigEnv } from 'vite';
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

export default defineConfig(
  ({ command, mode, isSsrBuild, isPreview }: ConfigEnv) => {
    // const isProduction = mode === 'production';
    // const isDevelopment = command === 'serve';

    return {
      plugins: [
        Vue(),
        AutoImport({
          dts: './assets/auto-imports.d.ts', // enable typescript support
          imports: ['vue', '@vueuse/core', 'pinia'],
        }),
        Icons({ compiler: 'vue3', scale: 1 }),
        Components({
          dts: './assets/icons.d.ts', // enable typescript support (create on build)
          version: 3,
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
          /**
           * or define custom path for your controllers.json
           * stimulus: './assets/other-dir/controllers.json
           */
          stimulus: {
            controllersDir: './assets/controllers',
          },

          // as we set `server.host` to 0.0.0.0
          // we must explicitly set the server host name
          viteDevServerHostname: 'localhost',
        }),
        tsconfigPaths(),
        tailwindcss(),
      ],
      publicDir: 'public',
      base: '/build',
      css: {
        preprocessorOptions: {
          scss: {
            api: 'modern-compiler',
          },
        },
      },
      build: {
        assetsInlineLimit: 512,
        outDir: 'public/build',
        manifest: true,
        emptyOutDir: true,
        // minify: false,
        rollupOptions: {
          input: {
            login: './assets/styles/pages/login.scss',
            main: './assets/vue/main.ts',

            app: './assets/app.ts',
            global: './assets/styles/global.scss',
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
        headers: {
          'Access-Control-Allow-Origin': '*',
        },
        port: 3000,
        cors: true,
        https: false,
        hmr: {
          host: '192.168.178.37', // localhost or your Docker host IP
          protocol: 'ws',
        },
        fs: {
          allow: ['.'],
        },
        watch: {
          ignored: ['**/.idea/**', '**/tests/**', '**/var/**', '**/vendor/**'],
        },
      },
      preview: {
        host: '0.0.0.0',
        headers: {
          'Access-Control-Allow-Origin': '*',
        },
        port: 3005,
        cors: true,
        https: false,
        hmr: {
          host: '192.168.178.37', // localhost or your Docker host IP
          protocol: 'ws',
        },
      },
    };
  },
);

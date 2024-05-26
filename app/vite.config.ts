import { defineConfig } from 'vite';
import { resolve } from 'node:path';
import { fileURLToPath, URL } from 'node:url';
import vue from '@vitejs/plugin-vue';
import symfonyPlugin from 'vite-plugin-symfony';
import Components from 'unplugin-vue-components/vite';
import Vuetify, { transformAssetUrls } from 'vite-plugin-vuetify';
import ViteFonts from 'unplugin-fonts/vite';

const pathResolve = (dir: string) => {
  return resolve(__dirname, dir);
};

export default defineConfig({
  plugins: [
    vue({
      template: { transformAssetUrls },
    }),
    // https://github.com/vuetifyjs/vuetify-loader/tree/master/packages/vite-plugin#readme
    Vuetify(),
    Components({}),
    ViteFonts({
      google: {
        families: [
          {
            name: 'Roboto',
            styles: 'wght@100;300;400;500;700;900',
          },
        ],
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
    },
    extensions: ['.js', '.json', '.jsx', '.mjs', '.ts', '.tsx', '.vue'],
  },
  build: {
    assetsInlineLimit: 512,
    manifest: true,
    rollupOptions: {
      input: {
        app: './assets/app.ts',
        'vue-app': './assets/vue/main.ts',
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

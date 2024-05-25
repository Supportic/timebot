import { resolve } from 'node:path';
import { defineConfig } from "vite";
import { fileURLToPath, URL } from "node:url";
import symfonyPlugin from "vite-plugin-symfony";
import vue from '@vitejs/plugin-vue';

/* if you're using React */
// import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        vue(),
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
            "@": fileURLToPath(new URL("./assets", import.meta.url)),
            '@components': resolve(__dirname, './assets/vue/components'),
            '@bootstrap': resolve(__dirname, './node_modules/bootstrap'),
        }
    },
    build: {
        assetsInlineLimit: 512,
        manifest: true,
        rollupOptions: {
            input: {
                app: "./assets/app.js",
                "vue-app": "./assets/vue/main.js",
                theme: "./assets/styles/theme.scss"
            },
            output: {
                manualChunks: {
                    vue: ['vue'],
                    bootstrap: ['bootstrap'],
                }
            }
        }
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
        }
    },
});

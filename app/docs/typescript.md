# Typescript

## TSConfig

We cannot shortcut to `@types/*` because it is already pointing to `node_modules`. Therefore use another or no symbol infront.
There are problems when importing a package like `@symfony/ux-test` because it tries to resolve the @ path relatively. However this is solved by installing the below mentioned package: vite-tsconfig-paths.

Resolve imports for typescript language server: `tsconfig.json`

```json
{
  "compilerOptions": {
    "paths": {
      "@/*": ["./assets/*"],
      "@components/*": ["./assets/vue/components/*"],
      "types/*": ["./assets/scripts/types/*"],
      "interfaces/*": ["./assets/scripts/interfaces/*"]
      // "@assets/*": ["./assets/vue/assets/*"],
      // "@store/*": ["./assets/vue/store/*"]
    }
  },
  "include": [
    "assets/**/*.ts",
    "assets/**/*.tsx",
    "assets/**/*.vue",
    ".d.ts",
    "vite-env.d.ts"
    // "tests/**/*.ts",
    // "tests/**/*.tsx"
  ],
  "exclude": ["public/build", "node_modules"]
}
```

Resolve imports for files in vite: `vite.config.ts`  
This setting can be completely skipped by installing this package: [vite-tsconfig-paths](https://www.npmjs.com/package/vite-tsconfig-paths) `npm i -D vite-tsconfig-paths`  
If you want to resolve Vue files, add `"allowJs": true`, in your tsconfig compilerOptions.

```js
export default defineConfig({
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./assets', import.meta.url)),
      '@components': pathResolve('./assets/vue/components'),
      '@styles': pathResolve('./assets/styles'),
      '@fonts': pathResolve('./assets/fonts'),
      types: pathResolve('./assets/scripts/types'),
      interfaces: pathResolve('./assets/scripts/interfaces'),
    },
    extensions: ['.js', '.json', '.jsx', '.mjs', '.ts', '.tsx', '.vue'],
  },
});
```

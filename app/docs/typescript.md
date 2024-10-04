# Typescript

## TSConfig

We cannot shortcut to `@types/*` because it is already pointing to `node_modules`. Therefore use another or no symbol infront.

Resolve imports for files in vite: `vite.config.ts`

```js
export default defineConfig({
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./assets", import.meta.url)),
      "@components": pathResolve("./assets/vue/components"),
      "@styles": pathResolve("./assets/styles"),
      "@fonts": pathResolve("./assets/fonts"),
      types: pathResolve("./assets/scripts/types"),
      interfaces: pathResolve("./assets/scripts/interfaces"),
    },
    extensions: [".js", ".json", ".jsx", ".mjs", ".ts", ".tsx", ".vue"],
  },
});
```

Resolve imports for typescript language server: `tsconfig.json`

```json
{
  "compilerOptions": {
    "baseUrl": ".",
    "paths": {
      "@/*": ["assets/*"],
      "@components/*": ["assets/vue/components/*"],
      "types/*": ["assets/scripts/types/*"],
      "interfaces/*": ["assets/scripts/interfaces/*"]
      // "@assets/*": ["assets/vue/assets/*"],
      // "@store/*": ["assets/vue/store/*"]
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

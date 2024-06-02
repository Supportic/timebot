# Vite

https://symfony-vite.pentatrion.com/

## Vue

Install @vitejs/plugin-vue to handle .vue files.

## Symfony UX

Is made to work with stimulus and has nothing todo with regular vue.

- Vue: https://symfony-vite.pentatrion.com/stimulus/symfony-ux.html

## Fonts

1. scss: fetch fonts manually

(carefull: fonts might flash on page load because of no preload feature)

Add `styles/_typography.scss` and `@forward 'typography'` in `theme.scss`.

```scss
@font-face {
  font-family: "Montserrat";
  font-style: normal;
  src: local("Montserrat"), url(@fonts/MontserratVariable.woff2) format("woff2");
}
@font-face {
  font-family: "MontserratItalic";
  font-style: italic;
  src: local("MontserratItalic"), url(@fonts/MontserratVariable-italic.woff2) format("woff2");
}

@font-face {
  font-family: "Inter";
  font-style: normal;
  src: local("Inter"), url(@fonts/InterVariable.woff2) format("woff2");
}
@font-face {
  font-family: "InterItalic";
  font-style: italic;
  src: local("InterItalic"), url(@fonts/InterVariable-italic.woff2) format("woff2");
}
```

2. vite: preload and fetch fonts manually

Install: `npm install vite-plugin-static-copy`

Adjust vite.config.ts:

```js
export default defineConfig({
  plugins: [
    viteStaticCopy({
      targets: [
        {
          src: "assets/fonts/*",
          dest: "fonts",
        },
      ],
    }),
  ],
});
```

Adjust base.html.twig header

```twig
<head>
    <link rel="preload" href="{{ asset('build/fonts/MontserratVariable.woff2') }}" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="{{ asset('build/fonts/MontserratVariable-italic.woff2') }}" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="{{ asset('build/fonts/InterVariable.woff2') }}" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="{{ asset('build/fonts/InterVariable-italic.woff2') }}" as="font" type="font/woff2" crossorigin />
</head>
```

3. vite: autoload and prefetch fonts

https://github.com/cssninjaStudio/unplugin-fonts?tab=readme-ov-file

This plugin goes beyond just generating font-face rules - it also takes care of link preload and prefetch, optimizing font loading for a faster and more efficient user experience.

install: `npm install unplugin-fonts/vite`

Add loading css from unfonts in app.ts: `import 'unfonts.css'`

Adjust vite.config.ts:

```js
import Unfonts from "unplugin-fonts/vite";

const fontFamilies = [
  {
    /**
     * Name of the font family.
     */
    name: "Montserrat",
    /**
     * Local name of the font. Used to add `src: local()` to `@font-rule`.
     */
    local: "MontserratVariable",
    /**
     * Regex(es) of font files to import. The names of the files will
     * predicate the `font-style` and `font-weight` values of the `@font-rule`'s.
     */
    src: "./assets/fonts/MontserratVariable.woff2",
  },
  {
    name: "MontserratItalic",
    local: "MontserratVariable-italic",
    src: "./assets/fonts/MontserratVariable-italic.woff2",
  },
  {
    name: "Inter",
    local: "InterVariable",
    src: "./assets/fonts/InterVariable.woff2",
  },
  {
    name: "InterItalic",
    local: "InterVariable-italic",
    src: "./assets/fonts/InterVariable-italic.woff2",
  },
];

export default defineConfig({
  plugins: [
    Unfonts({
      custom: {
        families: fontFamilies,

        /**
         * Defines the default `font-display` value used for the generated
         * `@font-rule` classes.
         */
        display: "auto",

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
        prefetch: false,

        /**
         * define where the font load tags should be inserted
         * default: 'head-prepend'
         *   values: 'head' | 'body' | 'head-prepend' | 'body-prepend'
         */
        injectTo: "head-prepend",
      },
    }),
  ],
});
```

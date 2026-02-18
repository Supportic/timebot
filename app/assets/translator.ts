/*
 * This file is part of the Symfony UX Translator package.
 *
 * If folder "../var/translations" does not exist, or some translations are missing,
 * you must warmup your Symfony cache to refresh JavaScript translations.
 *
 * If you use TypeScript, you can rename this file to "translator.ts" to take advantage of types checking.
 */

import { createTranslator } from '@symfony/ux-translator';
import { messages, localeFallbacks } from '../var/translations/index.js';

const translator = createTranslator({
  messages,
  localeFallbacks,
  throwWhenNotFound: true,
});

// Allow you to use `import { trans } from './translator';` in your assets
export const { trans } = translator;

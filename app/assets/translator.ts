import {
  trans,
  // getLocale,
  setLocaleFallbacks,
  throwWhenNotFound,
} from '@symfony/ux-translator';
import { localeFallbacks } from '../var/translations/configuration';
/*
 * This file is part of the Symfony UX Translator package.
 *
 * If folder "../var/translations" does not exist, or some translations are missing,
 * you must warmup your Symfony cache to refresh JavaScript translations.
 *
 * If you use TypeScript, you can rename this file to "translator.ts" to take advantage of types checking.
 */

setLocaleFallbacks(localeFallbacks);
// using this will throw an error when a translation key is missing
throwWhenNotFound(true);

// type Lang = 'en' | 'de' | undefined;

/**
 * ux-translator automatically detects the current language when using trans()
 * if you still want to read and set it, use this helper for the local param in trans function
 */
// const getCurrentLocale = (): Lang => {
//   return getLocale() as Lang;
// };

export { trans };
export * from '../var/translations';

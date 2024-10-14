// import this if not disabled in https://vite.dev/config/build-options#build-polyfillmodulepreload
import 'vite/modulepreload-polyfill';
import '@styles/pages/login.scss';

import {
  trans,
  LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_REVEAL,
  LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_CONCEAL,
} from '@/translator';

window.addEventListener('DOMContentLoaded', (_: Event) => {
  new PasswordFieldToggler('password', 'reveal-password-btn');
});

class PasswordFieldToggler {
  private passwordInput: HTMLInputElement | null | undefined;
  private revealPasswordButton: HTMLButtonElement | null | undefined;

  constructor(inputId: string, buttonId: string) {
    this.passwordInput = document.getElementById(
      inputId
    ) as HTMLInputElement | null;
    this.revealPasswordButton = document.getElementById(
      buttonId
    ) as HTMLButtonElement | null;

    if (
      !(this.passwordInput instanceof HTMLInputElement) ||
      !(this.revealPasswordButton instanceof HTMLButtonElement)
    ) {
      return;
    }

    this.revealPasswordButton.addEventListener('click', this.clickHandler);
  }

  private clickHandler = (_: MouseEvent): void => {
    if (this.passwordInput!.type === 'password') {
      this.passwordInput!.type = 'text';
      this.revealPasswordButton!.title = trans(
        LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_CONCEAL,
        {},
        'login',
      );
      return;
    }

    this.passwordInput!.type = 'password';
    this.revealPasswordButton!.title = trans(
      LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_REVEAL,
      {},
      'login',
    );
  };
}

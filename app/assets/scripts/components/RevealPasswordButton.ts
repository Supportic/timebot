import {
  trans,
  LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_REVEAL,
  LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_CONCEAL,
} from '@/translator';

export default class RevealPasswordButton {
  private isPasswordVisible: boolean = false;

  private PasswordInputId: string = 'password';
  private ButtonId: string = 'revealPasswordButton';
  private ButtonIconOnId: string = 'revealPasswordButtonIconOn';
  private ButtonIconOffId: string = 'revealPasswordButtonIconOff';
  private ButtonTitleId: string = 'revealPasswordButtonTitle';

  private PasswordInput!: HTMLInputElement;
  private Button!: HTMLElement;
  private IconOn!: HTMLElement;
  private IconOff!: HTMLElement;
  private ButtonTitle!: HTMLElement;

  constructor() {
    const passwordInput = document.getElementById(
      this.PasswordInputId,
    ) as HTMLInputElement | null;

    const button = document.getElementById(
      this.ButtonId,
    ) as HTMLButtonElement | null;

    if (
      !(passwordInput instanceof HTMLInputElement) ||
      !(button instanceof HTMLButtonElement)
    ) {
      return;
    }

    this.PasswordInput = passwordInput;
    this.Button = button;

    this.IconOff = document.getElementById(this.ButtonIconOffId) as HTMLElement;
    this.IconOn = document.getElementById(this.ButtonIconOnId) as HTMLElement;
    this.ButtonTitle = document.getElementById(
      this.ButtonTitleId,
    ) as HTMLElement;

    this.Button.addEventListener('click', this.toggle);
  }

  public toggle = (): void => {
    this.isPasswordVisible ? this.concealPassword() : this.revealPassword();
  };

  public revealPassword = (): void => {
    this.PasswordInput.type = 'text';
    const title = trans(
      LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_CONCEAL,
      {},
      'login',
    );
    this.Button.title = title;
    this.ButtonTitle.textContent = title;
    this.IconOff.classList.add('hidden');
    this.IconOn.classList.remove('hidden');
    this.isPasswordVisible = true;
  };

  public concealPassword = (): void => {
    this.PasswordInput.type = 'password';
    const title = trans(
      LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_REVEAL,
      {},
      'login',
    );
    this.Button.title = title;
    this.ButtonTitle.textContent = title;
    this.IconOn.classList.add('hidden');
    this.IconOff.classList.remove('hidden');
    this.isPasswordVisible = false;
  };
}

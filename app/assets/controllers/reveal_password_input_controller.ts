import { Controller } from '@hotwired/stimulus';
import { trans } from '@/translator';

// https://symfony.com/bundles/StimulusBundle/current/index.html#stimulus-twig-helpers
// https://ux.symfony.com/stimulus
// https://stimulus.hotwired.dev/reference/using-typescript

/**
 * disable controller if neeeded
 */
// import.meta.stimulusEnabled = false;
// import.meta.stimulusIdentifier = 'reveal-password-input';

export default class extends Controller {
  declare class: string;
  declare hidden: boolean;

  static values = {};
  static outlets = [];

  static targets: string[] = ['input', 'button', 'buttonLabel', 'icon'];

  declare inputTarget: HTMLInputElement;
  declare buttonTarget: HTMLButtonElement;
  declare buttonLabelTarget: HTMLElement;
  declare iconTargets: SVGElement[];

  static classes: string[] = ['hidden'];

  declare hasHiddenClass: boolean;
  declare hiddenClass: string;

  // stimulus connect lifecycle method
  public async connect() {
    this.hidden = this.inputTarget.type === 'password';
    this.class = this.hasHiddenClass ? this.hiddenClass : 'hidden';
  }

  public togglePassword = (e: PointerEvent): void => {
    e.preventDefault();

    this.inputTarget.type = this.hidden ? 'text' : 'password';
    const title = trans(
      this.hidden
        ? 'login.form_field.reveal_password_button.title_conceal'
        : 'login.form_field.reveal_password_button.title_reveal',
      {},
      'login',
    );
    this.buttonTarget.title = title;
    this.buttonLabelTarget.textContent = title;
    this.hidden = !this.hidden;

    this.iconTargets.forEach((icon) => icon.classList.toggle(this.class));
  };
}

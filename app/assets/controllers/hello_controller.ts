import { Controller } from '@hotwired/stimulus';

/**
 * disable controller if neeeded
 */
// import.meta.stimulusEnabled = false;
// import.meta.stimulusIdentifier = 'hello';

// https://symfony.com/bundles/StimulusBundle/current/index.html#stimulus-twig-helpers
// https://ux.symfony.com/stimulus
// https://stimulus.hotwired.dev/reference/using-typescript
export default class extends Controller {
  static targets: string[] = ['output'];

  declare readonly hasOutputTarget: boolean;
  declare readonly outputTarget: HTMLElement;
  declare readonly outputTargets: HTMLElement[];

  connect() {
    /**
     * You are overriding the content, you idiot!
     */
    // this.element.textContent = '';

    console.log((this.element as HTMLElement).dataset);

    if (this.hasOutputTarget) {
      console.log(this.outputTarget);
    }
  }
}

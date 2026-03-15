import { Controller } from '@hotwired/stimulus';

// https://symfony.com/bundles/StimulusBundle/current/index.html#stimulus-twig-helpers
// https://ux.symfony.com/stimulus
// https://stimulus.hotwired.dev/reference/using-typescript

/**
 * disable controller if neeeded
 */
// import.meta.stimulusEnabled = false;
// import.meta.stimulusIdentifier = 'app';

export default class extends Controller {
  static targets: string[] = ['sidebar'];

  declare readonly hasSidebarTarget: boolean;
  declare readonly sidebarTarget: HTMLElement;

  static values = {};
  static outlets = [];

  // stimulus initialize lifecycle method
  public async initialize() {}

  public toggleSidebarVisibility = (_: PointerEvent): void => {
    const isOpen = this.sidebarTarget.getAttribute('data-sidebar-open') === 'true';

    this.sidebarTarget.setAttribute(
      'data-sidebar-open',
      isOpen ? 'false' : 'true',
    );

    isOpen
      ? this.element.classList.remove('overlay')
      : this.element.classList.add('overlay');
  };
}

import { Controller } from '@hotwired/stimulus';

// https://symfony.com/bundles/StimulusBundle/current/index.html#stimulus-twig-helpers
// https://ux.symfony.com/stimulus
// https://stimulus.hotwired.dev/reference/using-typescript

/**
 * disable controller if neeeded
 */
// import.meta.stimulusEnabled = false;
// import.meta.stimulusIdentifier = 'app';

export default class App extends Controller {
  static values = {
    sidebarVisibleMobile: Boolean,
  };

  declare readonly hasSidebarVisibleValue: Boolean;
  declare sidebarVisibleMobileValue: boolean;

  static targets: string[] = ['sidebar'];

  declare readonly hasSidebarTarget: Boolean;
  declare readonly sidebarTarget: HTMLElement;

  static outlets = [];

  // stimulus initialize lifecycle method
  public async initialize() {}

  public toggleSidebarMobile = (): void => {
    this.sidebarVisibleMobileValue = !this.sidebarVisibleMobileValue;
  };

  public showSidebarMobile(): void {
    this.sidebarVisibleMobileValue = true;
  }

  public hideSidebarMobile(): void {
    this.sidebarVisibleMobileValue = false;
  }

  sidebarVisibleMobileValueChanged(value: boolean, _: boolean): void {
    this.sidebarTarget.setAttribute(
      'data-sidebar-visible-mobile',
      value ? 'true' : 'false',
    );
  }
}

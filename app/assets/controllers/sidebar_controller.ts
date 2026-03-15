import { Controller } from '@hotwired/stimulus';
import { getComponent, Component } from '@symfony/ux-live-component';

// https://symfony.com/bundles/StimulusBundle/current/index.html#stimulus-twig-helpers
// https://ux.symfony.com/stimulus
// https://stimulus.hotwired.dev/reference/using-typescript

/**
 * disable controller if neeeded
 */
// import.meta.stimulusEnabled = false;
// import.meta.stimulusIdentifier = 'sidebar';

export default class extends Controller {
  private component!: Component;
  private resizeObserver: ResizeObserver | null = null;
  private resizeTimeout: number | null = null;

  static targets: string[] = ['profileMenu'];

  declare readonly hasProfileMenuTarget: boolean;
  declare readonly profileMenuTarget: HTMLElement;
  // declare readonly profileMenuTargets: HTMLElement[];

  static values = {
    isExpanded: Boolean,
  };

  private readonly RESIZE_DEBOUNCE_MS: number = 100;

  declare readonly hasIsExpandedValue: Boolean;
  declare isExpandedValue: boolean;

  static outlets = [];

  // stimulus initialize lifecycle method
  public async initialize() {
    this.component = await getComponent(this.element as HTMLElement);

    this.initializeResizeObserver();
  }

  public disconnect(): void {
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
    if (this.resizeTimeout) {
      clearTimeout(this.resizeTimeout);
    }
  }

  public async toggleSidebarSize() {
    this.isExpandedValue = !this.isExpandedValue;
    // improve feeling and change state without request
    this.element.setAttribute(
      'data-sidebar-expanded',
      this.isExpandedValue ? 'true' : 'false',
    );
    this.component.action('saveSidebarStateInSession', {
      isExpanded: this.isExpandedValue,
    });
  }

  private minimizeSidebar = (): void => {
    this.isExpandedValue = false;
    // improve feeling and change state without request
    this.element.setAttribute('data-sidebar-expanded', 'false');
    this.component.action('saveSidebarStateInSession', {
      isExpanded: false,
    });
  };

  // Minimize sidebar when window resizes below desktop threshold and above mobile threshold
  private initializeResizeObserver = (): void => {
    const MOBILE_BREAKPOINT = 768; // md breakpoint
    const DESKTOP_BREAKPOINT = 1280; // xl breakpoint
    let previousWidth = window.innerWidth;

    this.resizeObserver = new ResizeObserver(() => {
      // Clear existing timeout
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }

      this.resizeTimeout = window.setTimeout(() => {
        const currentWidth = window.innerWidth;

        // Minimize if crossing desktop threshold from large to small
        const crossedDesktopThreshold =
          previousWidth >= DESKTOP_BREAKPOINT &&
          currentWidth < DESKTOP_BREAKPOINT;

        // Minimize if crossing mobile threshold from small to large (going upwards)
        const crossedMobileThresholdUp =
          previousWidth < MOBILE_BREAKPOINT &&
          currentWidth >= MOBILE_BREAKPOINT;

        if (crossedDesktopThreshold || crossedMobileThresholdUp) {
          this.minimizeSidebar();
        }

        previousWidth = currentWidth;
      }, this.RESIZE_DEBOUNCE_MS);
    });

    this.resizeObserver.observe(document.body);
  };

  public toggleSidebarProfileMenu(_: PointerEvent): void {
    this.profileMenuTarget.classList.toggle('open');
  }

  /**
   * Close the profile menu when you click somewhere else on the page.
   */
  public closeSidebarProfileMenu = (event: FocusEvent): void => {
    const relatedTarget = event.relatedTarget as HTMLElement | null;

    if (!this.profileMenuTarget.classList.contains('open')) return;

    // TODO debate wether to close it only on mouse events
    // NOTE relatedTarget is null if you click somehwere on the page, on a non-focusable element
    if (
      relatedTarget === null ||
      !this.profileMenuTarget.contains(relatedTarget)
    ) {
      this.profileMenuTarget.classList.remove('open');
    }
  };
}

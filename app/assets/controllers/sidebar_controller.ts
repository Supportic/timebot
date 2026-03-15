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
    autoMinimizeBreakpoint: Number,
    isExpanded: Boolean,
  };

  declare readonly hasAutoMinimizeBreakpointValue: Boolean;
  declare autoMinimizeBreakpointValue: number;
  private readonly RESIZE_DEBOUNCE_MS: number = 100;

  declare readonly hasIsExpandedValue: Boolean;
  declare isExpandedValue: boolean;

  static outlets = [];


  // stimulus initialize lifecycle method
  public async initialize() {
    this.component = await getComponent(this.element as HTMLElement);

    this.initializeResizeObserver();
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

  // Minimize sidebar when window resizes below threshold with debouncing
  private initializeResizeObserver = (): void => {
    let previousWidth = window.innerWidth;

    this.resizeObserver = new ResizeObserver(() => {
      // Clear existing timeout
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }

      this.resizeTimeout = window.setTimeout(() => {
        const currentWidth = window.innerWidth;

        // Only minimize if we actually cross the threshold from large to small
        const crossedThreshold =
          previousWidth >= this.autoMinimizeBreakpointValue &&
          currentWidth < this.autoMinimizeBreakpointValue;

        if (crossedThreshold) {
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

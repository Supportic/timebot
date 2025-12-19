import SessionUpdater from '@scripts/utils/SessionUpdater';

export default class Sidebar {
  private readonly BREAKPOINT_MD: number = 768;
  public readonly SIDEBAR_STATE_EXPANDED: string = 'expanded';
  public readonly SIDEBAR_STATE_MINIMIZED: string = 'minimized';

  private SidebarId: string = 'sidebar';
  private SidebarToggleButtonId: string = 'sidebar-toggle';
  private SidebarProfileButtonId: string = 'sidebar-profile-button';

  private Sidebar!: HTMLDivElement;
  private SidebarToggleButton!: HTMLButtonElement;
  private SidebarProfileButton!: HTMLButtonElement;

  constructor() {
    const initError = this.initialize();

    if (initError instanceof Error) {
      throw initError;
    }

    this.initializeResizeObserver();
    this.SidebarToggleButton.addEventListener('click', this.toggleSidebar);
    this.SidebarProfileButton.addEventListener(
      'click',
      this.toggleSidebarProfileMenu,
    );

    const profileMenu = document.getElementById('sidebar-profile-menu');
    profileMenu?.addEventListener('focusout', this.closeSidebarProfileMenu);
  }

  private initialize(): Error | null {
    const sidebar = document.getElementById(
      this.SidebarId,
    ) as HTMLDivElement | null;

    const sidebarToggleButton = document.getElementById(
      this.SidebarToggleButtonId,
    ) as HTMLButtonElement | null;

    const sidebarProfileButton = document.getElementById(
      this.SidebarProfileButtonId,
    ) as HTMLButtonElement | null;

    if (
      !(sidebar instanceof HTMLDivElement) ||
      !(sidebarToggleButton instanceof HTMLButtonElement) ||
      !(sidebarProfileButton instanceof HTMLButtonElement)
    ) {
      return new Error(
        'Failed to initialize Sidebar component: Missing required elements.',
      );
    }

    this.Sidebar = sidebar;
    this.SidebarToggleButton = sidebarToggleButton;
    this.SidebarProfileButton = sidebarProfileButton;

    return null;
  }

  public isExpanded = (): boolean => {
    return this.Sidebar.dataset.state === this.SIDEBAR_STATE_EXPANDED;
  };

  public expand = () => {
    this.Sidebar.dataset.state = this.SIDEBAR_STATE_EXPANDED;
    SessionUpdater.update('sidebar_expanded', true);
  };

  public minimize = () => {
    this.Sidebar.dataset.state = this.SIDEBAR_STATE_MINIMIZED;
    SessionUpdater.update('sidebar_expanded', false);
  };

  public toggleSidebar = (): void => {
    this.isExpanded() ? this.minimize() : this.expand();
  };

  // minimize sidebar when user is resizing window to mobile viewport
  private initializeResizeObserver = (): void => {
    // prevent auto closing when already on mobile viewport and viewport is resized
    let shoudMinimize = false;

    const resizeObserver = new ResizeObserver((entries) => {
      for (const entry of entries) {
        if (!entry.contentRect) {
          continue;
        }

        // only set when it satisfies the viewport
        if (entry.contentRect.width >= this.BREAKPOINT_MD) {
          shoudMinimize = true;
        }

        if (
          this.isExpanded() &&
          entry.contentRect.width < this.BREAKPOINT_MD &&
          shoudMinimize
        ) {
          this.minimize();
          shoudMinimize = false;
        }
      }
    });

    resizeObserver.observe(document.body);
  };

  private toggleSidebarProfileMenu = (): void => {
    const profileMenu = document.getElementById('sidebar-profile-menu');

    if (!(profileMenu instanceof HTMLElement)) {
      throw new Error(
        'Failed to toggle sidebar profile menu: Element not found.',
      );
    }

    profileMenu.classList.toggle('open');
  };

  private closeSidebarProfileMenu = (event: FocusEvent): void => {
    const target = event.currentTarget as HTMLElement;
    const relatedTarget = event.relatedTarget as HTMLElement | null;

    if (!target.classList.contains('open')) return;

    // TODO debate wether to close it only on mouse events
    // NOTE relatedTarget is null if you click somehwere on the page, on a non-focusable element
    if (relatedTarget === null || !target.contains(relatedTarget)) {
      target.classList.remove('open');
    }
  };
}

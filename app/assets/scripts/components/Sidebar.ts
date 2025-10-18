import SessionUpdater from '@/scripts/utils/SessionUpdater';

export default class Sidebar {
  private readonly BREAKPOINT_MD: number = 768;
  private readonly SIDEBAR_STATE_EXPANDED: string = 'expanded';
  private readonly SIDEBAR_STATE_MINIMIZED: string = 'minimized';

  private SidebarId: string = 'sidebar';
  private SidebarToggleButtonId: string = 'sidebar-toggle';

  private Sidebar!: HTMLDivElement;
  private SidebarToggleButton!: HTMLButtonElement;

  constructor() {
    const sidebar = document.getElementById(
      this.SidebarId,
    ) as HTMLDivElement | null;

    const sidebarToggleButton = document.getElementById(
      this.SidebarToggleButtonId,
    ) as HTMLButtonElement | null;

    if (
      !(sidebar instanceof HTMLDivElement) ||
      !(sidebarToggleButton instanceof HTMLButtonElement)
    ) {
      return;
    }

    this.Sidebar = sidebar;
    this.SidebarToggleButton = sidebarToggleButton;

    this.initializeResizeObserver();
    this.SidebarToggleButton.addEventListener('click', this.toggleSidebar);
  }

  public isExpanded = (): boolean => {
    return this.Sidebar.dataset.state === this.SIDEBAR_STATE_EXPANDED;
  };

  expand = () => {
    this.Sidebar.dataset.state = this.SIDEBAR_STATE_EXPANDED;

    SessionUpdater.update('sidebar_expanded', true);
  };

  minimize = () => {
    this.Sidebar.dataset.state = this.SIDEBAR_STATE_MINIMIZED;

    SessionUpdater.update('sidebar_expanded', false);
  };

  public toggleSidebar = (): void => {
    this.isExpanded() ? this.minimize() : this.expand();
  };

  // minimize sidebar when user is resizing window to mobile viewport
  private initializeResizeObserver = (): void => {
    const resizeObserver = new ResizeObserver((entries) => {
      for (const entry of entries) {
        if (
          entry.contentRect &&
          this.isExpanded() &&
          entry.contentRect.width < this.BREAKPOINT_MD
        ) {
          this.minimize();
        }
      }
    });

    resizeObserver.observe(document.body);
  };
}

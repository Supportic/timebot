import SessionUpdater from '@/scripts/utils/SessionUpdater';

export default class Sidebar {
  private readonly BREAKPOINT_MD: number = 768;
  private readonly SIDEBAR_OPEN_CLASS: string = 'open';

  private SidebarId: string = 'sidebar';
  private SidebarToggleButtonId: string = 'sidebar-toggle';
  private SidebarToggleIconOpenedId: string = 'sidebar-toggle-icon-opened';
  private SidebarToggleIconClosedId: string = 'sidebar-toggle-icon-closed';
  private AppNameId: string = 'sidebar-app-name';
  private AppProfileId: string = 'sidebar-user-profile';
  private NavLinksSelector: string = '.nav-link span';
  private TooltipsSelector: string = '.sidebar-tooltip';

  private Sidebar!: HTMLDivElement;
  private SidebarToggleButton!: HTMLButtonElement;
  private SidebarToggleIconOpened!: HTMLElement;
  private SidebarToggleIconClosed!: HTMLElement;
  private AppName!: HTMLSpanElement;
  private AppProfile!: HTMLDivElement;
  private NavLinks: HTMLSpanElement[] = [];
  private Tooltips: HTMLDivElement[] = [];

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

    this.SidebarToggleIconOpened = document.getElementById(
      this.SidebarToggleIconOpenedId,
    ) as HTMLElement;
    this.SidebarToggleIconClosed = document.getElementById(
      this.SidebarToggleIconClosedId,
    ) as HTMLElement;
    this.AppName = document.getElementById(this.AppNameId) as HTMLSpanElement;
    this.AppProfile = document.getElementById(
      this.AppProfileId,
    ) as HTMLDivElement;
    this.NavLinks = [
      ...document.querySelectorAll<HTMLSpanElement>(this.NavLinksSelector),
    ];
    this.Tooltips = [
      ...document.querySelectorAll<HTMLDivElement>(this.TooltipsSelector),
    ];

    this.initializeResizeObserver();
    this.SidebarToggleButton.addEventListener('click', this.toggleSidebar);
  }

  public isExpanded = (): boolean => {
    return this.Sidebar.classList.contains(this.SIDEBAR_OPEN_CLASS);
  };

  expand = () => {
    this.Sidebar.classList.add(this.SIDEBAR_OPEN_CLASS);

    this.SidebarToggleIconOpened.classList.remove('hidden');
    this.SidebarToggleIconClosed.classList.add('hidden');

    this.AppName.classList.replace('w-0', 'w-32');
    this.AppName.nextElementSibling?.classList.remove('mr-auto');

    this.AppProfile.classList.add('w-52', 'ml-3');
    this.AppProfile.classList.remove('w-0');

    this.NavLinks.forEach((navLink: HTMLSpanElement) => {
      navLink.classList.add('w-52', 'ml-3');
      navLink.classList.remove('w-0');
    });

    this.Tooltips.forEach((tooltip: HTMLDivElement) => {
      tooltip.classList.add('hidden');
    });

    SessionUpdater.update('sidebar_expanded', true);
  };

  minimize = () => {
    this.Sidebar.classList.remove(this.SIDEBAR_OPEN_CLASS);

    this.SidebarToggleIconOpened.classList.add('hidden');
    this.SidebarToggleIconClosed.classList.remove('hidden');

    this.AppName.classList.replace('w-32', 'w-0');
    this.AppName.nextElementSibling?.classList.add('mr-auto');

    this.AppProfile.classList.add('w-52', 'ml-3');
    this.AppProfile.classList.remove('w-0');

    this.AppProfile.classList.add('w-0');
    this.AppProfile.classList.remove('w-52', 'ml-3');

    this.NavLinks.forEach((navLink: HTMLSpanElement) => {
      navLink.classList.add('w-0');
      navLink.classList.remove('w-52', 'ml-3');
    });

    this.Tooltips.forEach((tooltip: HTMLDivElement) => {
      tooltip.classList.remove('hidden');
    });

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

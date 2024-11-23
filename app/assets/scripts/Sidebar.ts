export default class SidebarToggle {
  private sidebar: HTMLElement | null;
  private sidebarToggle: HTMLElement | null;

  private isExpanded: boolean = true;

  constructor(sidebarId: string, sidebarToggleId: string) {
    this.sidebar = document.getElementById(sidebarId);
    this.sidebarToggle = document.getElementById(sidebarToggleId);

    if (!this.sidebar) {
      console.error(`Element with id '${sidebarId}' does not exist.`);
      return;
    }

    if (!this.sidebarToggle) {
      console.error(`Element with id '${sidebarToggleId}' does not exist.`);
      return;
    }

    this.expand();

    this.sidebarToggle.addEventListener(
      'click',
      this.sidebarToggleClickHandler
    );
  }

  expand = () => {
    this.sidebar!.dataset.exanded = 'true';
    this.isExpanded = true;
  };
  contract = () => {
    this.sidebar!.dataset.exanded = 'false';
    this.isExpanded = false;
  };

  toggleSidebar() {
    this.isExpanded ? this.contract() : this.expand();
  }

  sidebarToggleClickHandler = () => {
    this.toggleSidebar();
  };
}

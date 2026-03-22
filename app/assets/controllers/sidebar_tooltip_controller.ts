import Sidebar from '@/controllers/sidebar_controller';
import { Controller } from '@hotwired/stimulus';

export default class SidebarTooltip extends Controller {
  // Stack to hold tooltips that are currently animating out
  private fadingTooltips: HTMLDivElement[] = [];

  private tooltip: HTMLDivElement | null = null;
  private hideTimeoutId: undefined | ReturnType<typeof setTimeout>;

  private readonly TOOLTIP_VISIBLE_CLASSES: string[] = [
    'opacity-100',
    'visible',
    'translate-x-0',
  ];
  private readonly TOOLTIP_INVISIBLE_CLASSES: string[] = [
    'opacity-0',
    'invisible',
    '-translate-x-3',
  ];
  private readonly TOOLTIP_STYLE: string =
    'px-2 py-1 font-medium bg-indigo-100 text-indigo-800 text-sm rounded-md';

  static values = {
    text: String,
    labelGapPx: {
      default: 12,
      type: Number,
    },
    stayOnHover: {
      default: true,
      type: Boolean,
    },
    fadeoutMs: {
      default: 100,
      type: Number,
    },
  };

  declare textValue: string;
  declare labelGapPxValue: number;
  declare fadeoutMsValue: number;
  declare stayOnHoverValue: boolean;

  static outlets = ['sidebar'];

  declare readonly hasSidebarOutlet: Boolean;
  declare sidebarOutlet: Sidebar;
  declare sidebarOutletElement: HTMLElement;

  public disconnect(): void {
    this.destroyTooltip();
  }

  public show = (): void => {
    // Clear the initial 100ms delay if hovered back quickly enough
    clearTimeout(this.hideTimeoutId);

    // Prevent showing if sidebar is expanded or on mobile viewport
    if (
      this.hasSidebarOutlet &&
      (this.sidebarOutlet.isExpandedValue ||
        this.sidebarOutletElement.dataset.sidebarVisibleMobile === 'true')
    ) {
      return;
    }

    if (!this.tooltip) {
      this.createTooltip();
    }

    if (this.tooltip) {
      const rect = this.element.getBoundingClientRect();

      const top = rect.top + rect.height / 2 - this.tooltip.offsetHeight / 2;
      const left = rect.right + this.labelGapPxValue;

      this.tooltip.style.top = `${top}px`;
      this.tooltip.style.left = `${left}px`;

      // travel path to avoid early closing of tooltip
      this.tooltip.style.setProperty(
        '--bridge-width',
        `${this.labelGapPxValue + 5}px`,
      );

      requestAnimationFrame(() => {
        this.tooltip?.classList.remove(...this.TOOLTIP_INVISIBLE_CLASSES);
        this.tooltip?.classList.add(...this.TOOLTIP_VISIBLE_CLASSES);
      });
    }
  };

  public hide = (): void => {
    this.hideTimeoutId = setTimeout(() => {
      if (this.tooltip) {
        // 1. Isolate the dying tooltip
        const expiringTooltip = this.tooltip;

        // 2. Remove listeners so hovering a fading tooltip doesn't trigger anything
        if (this.stayOnHoverValue) {
          expiringTooltip.removeEventListener(
            'mouseenter',
            this.onTooltipEnter,
          );
          expiringTooltip.removeEventListener(
            'mouseleave',
            this.onTooltipLeave,
          );
        }

        // 3. Decouple it from the controller so a fresh one can be made if needed
        this.tooltip = null;

        // 4. Push to the stack
        this.fadingTooltips.push(expiringTooltip);

        // 5. Trigger the fade out animation
        expiringTooltip.classList.remove(...this.TOOLTIP_VISIBLE_CLASSES);
        expiringTooltip.classList.add(...this.TOOLTIP_INVISIBLE_CLASSES);

        // 6. Remove from DOM and stack after transition finishes (matching duration-200)
        setTimeout(() => {
          expiringTooltip.remove();
          this.fadingTooltips = this.fadingTooltips.filter(
            (t) => t !== expiringTooltip,
          );
        }, 200);
      }
    }, this.fadeoutMsValue);
  };

  private createTooltip = (): void => {
    this.tooltip = document.createElement('div');
    this.tooltip.className = `
        ${this.TOOLTIP_STYLE}
        absolute z-2 whitespace-nowrap transition-all duration-200 opacity-0 invisible -translate-x-3
        before:content-[''] before:absolute before:top-0 before:h-full
        before:w-[var(--bridge-width)] before:-left-[var(--bridge-width)]
    `
      .replace(/\s+/g, ' ')
      .trim();

    this.tooltip.textContent = this.textValue;

    if (this.stayOnHoverValue) {
      this.tooltip.addEventListener('mouseenter', this.onTooltipEnter);
      this.tooltip.addEventListener('mouseleave', this.onTooltipLeave);
    }

    document.body.appendChild(this.tooltip);
  }

  private destroyTooltip = (): void => {
    if (this.tooltip) {
      if (this.stayOnHoverValue) {
        this.tooltip.removeEventListener('mouseenter', this.onTooltipEnter);
        this.tooltip.removeEventListener('mouseleave', this.onTooltipLeave);
      }
      this.tooltip.remove();
      this.tooltip = null;
    }
    clearTimeout(this.hideTimeoutId);
  }

  public onTooltipEnter = (): void => {
    clearTimeout(this.hideTimeoutId);
  };

  public onTooltipLeave = (): void => {
    this.hide();
  };
}

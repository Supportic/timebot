import { Controller } from '@hotwired/stimulus';
import { getComponent, Component } from '@symfony/ux-live-component';

// https://symfony.com/bundles/StimulusBundle/current/index.html#stimulus-twig-helpers
// https://ux.symfony.com/stimulus
// https://stimulus.hotwired.dev/reference/using-typescript

/**
 * disable controller if neeeded
 */
// import.meta.stimulusEnabled = false;
// import.meta.stimulusIdentifier = 'pagination';

/**
 * I implemented this controller to force a page number when the pagination is loaded without a query param and remove the query on the first page.
 * <twig:Table:UserTable :currentPage="4" />
 * If this is problematic in the future remove this TS controller and always provide URLs with query param.
 */
export default class extends Controller {
  private component!: Component;

  static values = {
    currentPage: Number,
    pageQueryAlias: String,
  };

  declare readonly hasCurrentPageValue: boolean;
  declare currentPageValue: number;

  declare readonly hasPageQueryAliasValue: boolean;
  declare readonly pageQueryAliasValue: string | null;

  // stimulus initialize lifecycle method
  async initialize() {
    this.component = await getComponent(this.element as HTMLElement);

    this.updateQueryParam();
  }

  // stimulus value changed callback
  public currentPageValueChanged() {
    this.updateQueryParam();
  }

  private updateQueryParam() {
    const pageQueryAlias =
      this.hasPageQueryAliasValue && this.pageQueryAliasValue !== null
        ? this.pageQueryAliasValue
        : 'p';

    const url = new URL(window.location.href);

    this.hasCurrentPageValue && this.currentPageValue > 1
      ? url.searchParams.set(pageQueryAlias, this.currentPageValue.toString())
      // delete param on page 1
      : url.searchParams.delete(pageQueryAlias);

    window.history.replaceState({}, '', url);
  }
}

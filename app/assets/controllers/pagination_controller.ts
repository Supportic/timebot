import { Controller } from '@hotwired/stimulus';
// import { getComponent, Component } from '@symfony/ux-live-component';

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
  // private component!: Component;

  static values = {
    currentPage: Number,
    customPageQueryAlias: String,
  };

  declare readonly hasCurrentPageValue: boolean;
  declare currentPageValue: number;

  declare readonly hasCustomPageQueryAliasValue: boolean;
  declare readonly customPageQueryAliasValue: string | null;

  // stimulus initialize lifecycle method
  async initialize() {
    // this.component = await getComponent(this.element as HTMLElement);

    this.updateQueryParam();
  }

  // stimulus value changed callback
  public currentPageValueChanged() {
    this.updateQueryParam();
  }

  private updateQueryParam() {
    const pageQueryAlias =
      this.hasCustomPageQueryAliasValue &&
      this.customPageQueryAliasValue !== null
        ? this.customPageQueryAliasValue
        : 'p';

    const url = new URL(window.location.href);
    const currentPage = url.searchParams.get(pageQueryAlias);
    const expectedValue =
      this.hasCurrentPageValue && this.currentPageValue > 1
        ? this.currentPageValue.toString()
        : null;

    // php updated the query param value already - no update needed
    if (currentPage === expectedValue) {
      return;
    }

    expectedValue !== null
      ? // update URL on page load without query param but forced page in template
        url.searchParams.set(pageQueryAlias, expectedValue)
      : // delete query param on page 1
        url.searchParams.delete(pageQueryAlias);

    window.history.replaceState({}, '', url);
  }
}

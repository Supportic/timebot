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
 * <twig:Pagination :currentPage="4" />
 * If this is problematic in the future remove this TS controller and always provide URLs with query param.
 */
export default class extends Controller {
  // private component!: Component;

  static values = {
    paginationPage: Number,
    paginationCustomQueryAlias: String,
  };

  declare readonly hasPaginationPageValue: boolean;
  declare paginationPageValue: number;

  declare readonly hasPaginationCustomQueryAliasValue: boolean;
  declare readonly paginationCustomQueryAliasValue: string | null;

  // stimulus initialize lifecycle method
  async initialize() {
    // this.component = await getComponent(this.element as HTMLElement);

    this.updateQueryParam();
  }

  // stimulus value changed callback
  public paginationPageValueChanged() {
    this.updateQueryParam();
  }

  private updateQueryParam() {
    const pageQueryAlias =
      this.hasPaginationCustomQueryAliasValue &&
      this.paginationCustomQueryAliasValue !== null
        ? this.paginationCustomQueryAliasValue
        : 'p';

    const url = new URL(window.location.href);
    const currentPage = url.searchParams.get(pageQueryAlias);
    const expectedValue =
      this.hasPaginationPageValue && this.paginationPageValue > 1
        ? this.paginationPageValue.toString()
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

import { Controller } from '@hotwired/stimulus';

import.meta.stimulusEnabled = false;

// https://stimulus.hotwired.dev/reference/using-typescript
export default class extends Controller {
  async connect() {
    // try {
    //   console.log('into clipboard: ' + this.element.textContent);
    //   await navigator.clipboard.writeText(this.element.textContent);
    // } catch (err) {
    //   console.error('Failed to copy text: ', err);
    // }
  }
}

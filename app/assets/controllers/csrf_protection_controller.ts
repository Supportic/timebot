const nameCheck = /^[-_a-zA-Z0-9]{4,22}$/;
const tokenCheck = /^[-_/+a-zA-Z0-9]{24,}$/;

// augment Window to include legacy IE msCrypto
declare global {
  interface Window {
    msCrypto?: Crypto;
  }
}

// Define the shape of Hotwire/Turbo custom events
interface TurboSubmitEvent extends CustomEvent {
  detail: {
    formSubmission: {
      formElement: HTMLFormElement;
      fetchRequest: {
        headers: Record<string, string>;
      };
    };
  };
}

// --- Event Listeners ---

// Generate and double-submit a CSRF token in a form field and a cookie
document.addEventListener('submit',function (event: Event): void {
    if (!(event.target instanceof HTMLFormElement)) {
      return;
    }

    generateCsrfToken(event.target);
  },
  true,
);

// When @hotwired/turbo handles form submissions, send the CSRF token in a header
document.addEventListener('turbo:submit-start', function (event: Event) {
  // Cast generic Event to our custom Turbo interface
  const turboEvent = event as TurboSubmitEvent;

  if (!turboEvent.detail?.formSubmission?.formElement) return;

  const h = generateCsrfHeaders(turboEvent.detail.formSubmission.formElement);

  Object.keys(h).forEach(function (k) {
    turboEvent.detail.formSubmission.fetchRequest.headers[k] = h[k];
  });
});

// When @hotwired/turbo handles form submissions, remove the CSRF cookie after submission
document.addEventListener('turbo:submit-end', function (event: Event) {
  const turboEvent = event as TurboSubmitEvent;

  if (!turboEvent.detail?.formSubmission?.formElement) return;

  removeCsrfToken(turboEvent.detail.formSubmission.formElement);
});

export function generateCsrfToken(formElement: HTMLFormElement): void {
  const csrfField = formElement.querySelector(
    'input[data-controller="csrf-protection"], input[name="_csrf_token"]',
  );

  if (!(csrfField instanceof HTMLInputElement)) {
    return;
  }

  let csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');
  let csrfToken = csrfField.value;

  if (!csrfCookie && nameCheck.test(csrfToken)) {
    csrfField.setAttribute(
      'data-csrf-protection-cookie-value',
      (csrfCookie = csrfToken),
    );

    const randomValues = (window.crypto || window.msCrypto!).getRandomValues(
      new Uint8Array(18),
    );

    const binaryString = String.fromCharCode.apply(
      null,
      Array.from(randomValues),
    );

    csrfField.defaultValue = csrfToken = btoa(binaryString);
  }

  csrfField.dispatchEvent(new Event('change', { bubbles: true }));

  if (csrfCookie && tokenCheck.test(csrfToken)) {
    const cookie =
      csrfCookie +
      '_' +
      csrfToken +
      '=' +
      csrfCookie +
      '; path=/; samesite=strict';

    document.cookie =
      window.location.protocol === 'https:'
        ? '__Host-' + cookie + '; secure'
        : cookie;
  }
}

export function generateCsrfHeaders(formElement: HTMLFormElement): Record<string, string> {
  const headers: Record<string, string> = {};
  const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

  if (!(csrfField instanceof HTMLInputElement)) {
    return headers;
  }

  const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

  if (typeof csrfCookie !== 'string' || csrfCookie === null) {
    return headers;
  }

  if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
    headers[csrfCookie] = csrfField.value;
  }

  return headers;
}

export function removeCsrfToken (formElement: HTMLFormElement) {
    const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

    if (!(csrfField instanceof HTMLInputElement)) {
        return;
    }

    const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

    if (typeof csrfCookie !== 'string' || null === csrfCookie) {
        return;
    }

    if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
        const cookie = csrfCookie + '_' + csrfField.value + '=0; path=/; samesite=strict; max-age=0';

        document.cookie = window.location.protocol === 'https:' ? '__Host-' + cookie + '; secure' : cookie;
    }
}

/* stimulusFetch: 'lazy' */
export default 'csrf-protection-controller';

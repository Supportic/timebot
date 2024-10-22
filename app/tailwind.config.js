/** @type {import('tailwindcss').Config} */

import primeUi from 'tailwindcss-primeui';

export default {
  plugins: [
    primeUi
  ],
  content: [
    "./assets/**/*.{js,ts,jsx,tsx,vue}",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'primary': 'rgb(15 23 42)',
        'danger': '#e64848'
      },
      borderRadius: {
        '4xl': '2rem',
      },
      ringWidth: {
        '6': '6px',
      },
      scale: {
        '140': '1.4',
      }
    },
  },
}


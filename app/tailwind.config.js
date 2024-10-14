/** @type {import('tailwindcss').Config} */
export default {
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
  plugins: [
  ],
}


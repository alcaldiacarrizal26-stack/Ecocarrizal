/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#05772a',
        'secondary': '#0e7970',
        'mint': '#78ad8b',
        'soft-green': '#aecbc5',
        'dark-green': '#347a4b',
        'teal': '#66a198',
        'gray-light': '#c5c6c6',
      }
    },
  },
  plugins: [],
}
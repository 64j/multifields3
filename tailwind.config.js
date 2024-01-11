/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',

  content: [
    './src/js/**'
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: '-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji"'
      },
      colors: {
        transparent: 'transparent',
        current: 'currentColor',
        white: '#ffffff',
        gray: {
          50: '#e2ebf1',
          100: '#c3cbd3',
          200: '#a5acb4',
          300: '#7f848c',
          400: '#61666e',
          500: '#4d5460',
          600: '#3f4550',
          700: '#282c34',
          750: '#23272e',
          800: '#202329',
          900: '#1a1c21',
          950: '#111417',
        },
        blue: {
          50: '#eef5ff',
          100: '#e0ecff',
          200: '#c7d8fe',
          300: '#a5c1fc',
          400: '#81a7f8',
          500: '#638bf1',
          600: '#4666e5',
          700: '#3850ca',
          800: '#3047a3',
          900: '#2e4681',
          950: '#1b254b',
        },
        green: {
          50: '#ecfdf3',
          100: '#d1fadb',
          200: '#a7f3c2',
          300: '#6ee7a2',
          400: '#34d374',
          500: '#10b965',
          600: '#05964e',
          700: '#047848',
          800: '#065f33',
          900: '#064e2e',
          950: '#022c1a',
        },
      },
    }
  },

  plugins: []
}

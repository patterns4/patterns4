const colors = require('./node_modules/tailwindcss/colors');

module.exports = {
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    colors: {
      sky: colors.sky,
      cyan: colors.cyan,
      gray: colors.gray,
      blue: colors.blue,
      teal: colors.teal,
      white: colors.white
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}

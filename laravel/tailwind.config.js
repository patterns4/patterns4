const colors = require('./node_modules/tailwindcss/colors');

module.exports = {
  purge:
        [
            "./resources/views/*.blade.php",
            "./resources/views/auth/*.blade.php",
            "./resources/views/layouts/*.blade.php",
            "./resources/views/api/*.blade.php"
        ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    colors: {
      sky: colors.sky,
      cyan: colors.cyan,
      gray: colors.gray,
      blue: colors.blue,
      teal: colors.teal,
      white: colors.white,
    },
    boxShadow: {
      'default': 'rgba(0, 0, 0, 0.24) 0px 3px 8px',
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}

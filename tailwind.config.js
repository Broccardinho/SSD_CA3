// tailwind.config.js
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                'press-start': ['"Press Start 2P"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'pokemon-yellow': '#ffcb05',
                'pokemon-blue': '#2a75bb',
            }
        },
    },
    plugins: [],
}

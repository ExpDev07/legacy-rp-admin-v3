const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [ 'Nunito', ...defaultTheme.fontFamily.sans ],
            },
            colors: {
                indigo: {
                    900: '#191e38',
                    800: '#2f365f',
                    600: '#5661b3',
                    500: '#6574cd',
                    400: '#7886d7',
                    300: '#b2b7ff',
                    100: '#e6e8ff',
                },
            },
        },
    },

    variants: {
        opacity: [
            'responsive',
            'hover',
            'focus',
            'disabled',
        ],
    },

};

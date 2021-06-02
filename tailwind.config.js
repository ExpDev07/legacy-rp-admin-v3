const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    mode: 'jit',
    darkMode: 'class',
    purge: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue'
    ],
    future: {
        removeDeprecatedGapUtilities: true,
        purgeLayersByDefault: true,
    },
    theme: {
        extend: {
            fontFamily: {
                'sans': [ 'Nunito', ...defaultTheme.fontFamily.sans ],
            },
            colors: {
                // Light & dark.
                'light': defaultTheme.colors.white,
                'dark':  defaultTheme.colors.gray['900'],

                // Theme colors.
                'primary':   defaultTheme.colors.indigo['600'],
                'secondary': defaultTheme.colors.gray['100'],
                'danger':    defaultTheme.colors.red['500'],
                'warning':   defaultTheme.colors.yellow['500'],
                'success':   defaultTheme.colors.green['500'],
                'muted':     defaultTheme.colors.gray['700'],

                // Theme pale colors.
                'primary-pale':   defaultTheme.colors.indigo['100'],
                'secondary-pale': defaultTheme.colors.gray['50'],
                'danger-pale':    defaultTheme.colors.red['100'],
                'warning-pale':   defaultTheme.colors.yellow['100'],
                'success-pale':   defaultTheme.colors.green['100'],

                // Theme colors (dark mode)
                'dark-primary':   defaultTheme.colors.indigo['300'],
                'dark-secondary': defaultTheme.colors.gray['700'],
                'dark-danger':    defaultTheme.colors.red['500'],
                'dark-warning':   defaultTheme.colors.yellow['500'],
                'dark-success':   defaultTheme.colors.green['500'],
                'dark-muted':     defaultTheme.colors.gray['50'],

                // Theme pale colors (dark mode)
                'dark-primary-pale':   defaultTheme.colors.indigo['700'],
                'dark-secondary-pale': defaultTheme.colors.gray['700'],
                'dark-danger-pale':    defaultTheme.colors.red['700'],
                'dark-warning-pale':   defaultTheme.colors.yellow['700'],
                'dark-success-pale':   defaultTheme.colors.green['700'],
            }
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
    ],
}

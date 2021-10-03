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
        screens: {
            'mobile': {
                'max': '640px',
            },
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
        },
        extend: {
            zIndex: {
                '1k': '1000',
                '2k': '2000'
            },
            fontSize: {
                'xxs': '11px',
            },
            fontFamily: {
                'sans': [ 'Nunito', ...defaultTheme.fontFamily.sans ],
            },
            lineHeight: {
                'map-icon': '20px',
            },
            width: {
                'inventory':          '220px',
                'alert':              '650px',
                'big-alert':          '650px',
                'character_advanced': '550px',
                '90':                 '90px',
                'map':                '1160px',
                'map-right':          'calc(100% - 1160px)',
                'split':              'calc(50% - 10px)',
                'tp':                 '170px',
                'tp-staff':           '200px',
                'inventory_contents': '660px',
                'inventory_slot':     '100px',
                'map-gauge':          '258px',
                'map-other-gauge':    'calc(100% - 125px)',
                'map-height-ind':     '60px',
                'map-icon':           '20px',
                'ch-button':          '32px',
            },
            height: {
                'side-close':      '40px',
                'side-open-one':   '116px',
                'side-open-two':   '134px',
                'side-open-three': '180px',
                'max':             'calc(100vh - (210px + 120px))',
                'inventory_slot':  '100px',
            },
            minWidth: {
                'input': '200px',
            },
            maxHeight: {
                'max': 'calc(100% - 60px)',
                'img': '500px'
            },
            listStyleType: {
                'dash': "'-'"
            },
            inset: {
                'attr':  '16.5px',
                'attr2': '118.5px',
            },
            colors: {
                // Light & dark.
                'light': defaultTheme.colors.white,
                'dark':  defaultTheme.colors.gray['900'],

                // Map colors
                'map-staff':     '#46A54B',
                'map-police':    '#7469FF',
                'map-ems':       '#FF5959',
                'map-highlight': '#FF6400',

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
                'dark-muted':     defaultTheme.colors.gray['300'],

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

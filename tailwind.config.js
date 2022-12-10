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
            '3xl': '1650px',
        },
        extend: {
            spacing: {
                '17': '4.25rem'
            },
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
                'small-alert':        '380px',
                'alert':              '650px',
                'big-alert':          '650px',
                'large-alert':        '1000px',
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
                'xs-steam':           '150px'
            },
            height: {
                'side-close':      '40px',
                'side-open-one':   '116px',
                'side-open-two':   '134px',
                'side-open-three': '180px',
                'side-open-four':  '226px',
                'side-open-five':  '272px',
                'max':             'calc(100vh - (210px + 120px))',
                'inventory_slot':  '100px'
            },
            minWidth: {
                'input': '200px',
            },
            minHeight: {
                '50': '50px'
            },
            maxHeight: {
                'max':       'calc(100% - 60px)',
                'img':       '500px',
                'modal-max': 'calc(100% - 10rem)'
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

                // Rose
                'rose-100': 'rgb(255, 228, 230)',
                'rose-200': 'rgb(254, 205, 211)',
                'rose-300': 'rgb(253, 164, 175)',
                'rose-400': 'rgb(251, 113, 133)',
                'rose-500': 'rgb(244, 63, 94)',
                'rose-600': 'rgb(225, 29, 72)',
                'rose-700': 'rgb(190, 18, 60)',
                'rose-800': 'rgb(159, 18, 57)',
                'rose-900': 'rgb(136, 19, 55)',

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

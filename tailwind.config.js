import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbitePlugin from 'flowbite/plugin';

export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                primary: {
                    DEFAULT: '#0F766E',
                    dark: '#115E59',
                    light: '#14B8A6',
                },
                'ui-canvas': '#EEF4F1',
                'ui-surface': '#F9FCFB',
                'ui-shell': '#D4E2DC',
                'ui-action': '#0B5D56',
                'ui-anchor': '#0F2F2C',
                'ui-border': '#DDE7E3',
                'ui-text': '#10201D',
                'ui-subtext': '#64748B',
                'ui-muted': '#EAF0EE',
                'ui-proof': '#0891B2',
                'ui-success': '#059669',
                'ui-warning': '#D97706',
                'ui-danger': '#E11D48',
            },
        },
    },

    plugins: [
        forms,
        flowbitePlugin,
    ],
};

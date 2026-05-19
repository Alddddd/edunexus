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
                    DEFAULT: '#064E3B',
                    dark: '#083B2E',
                    light: '#0F766E',
                },
                'ui-canvas': '#F3F7F4',
                'ui-surface': '#FFFFFF',
                'ui-shell': '#DCE8E1',
                'ui-action': '#064E3B',
                'ui-anchor': '#0B2F25',
                'ui-border': '#D5E2DC',
                'ui-text': '#10201D',
                'ui-subtext': '#53645E',
                'ui-muted': '#E7EFEA',
                'ui-proof': '#0891B2',
                'ui-success': '#047857',
                'ui-warning': '#B45309',
                'ui-danger': '#BE123C',
            },
        },
    },

    plugins: [
        forms,
        flowbitePlugin,
    ],
};

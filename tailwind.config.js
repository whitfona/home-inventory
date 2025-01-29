import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: 'rgba(var(--primary))',
                secondary: 'rgba(var(--secondary))',
                tertiary: 'rgba(var(--tertiary))',

                cta: 'rgba(var(--cta))',

                light: 'rgba(var(--light))',
                dark: 'rgba(var(--dark))',

                background: 'rgba(var(--background)',
                'background-hover': 'rgba(var(--background-hover))',
            },
        },
    },

    plugins: [forms],
};

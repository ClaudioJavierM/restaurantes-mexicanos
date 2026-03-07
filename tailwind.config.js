import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'grid-cols-1',
        'grid-cols-2',
        'grid-cols-3',
        'grid-cols-4',
        'sm:grid-cols-2',
        'sm:grid-cols-3',
        'sm:grid-cols-4',
        'md:grid-cols-2',
        'md:grid-cols-3',
        'md:grid-cols-4',
        'lg:grid-cols-2',
        'lg:grid-cols-3',
        'lg:grid-cols-4',
        'xl:grid-cols-2',
        'xl:grid-cols-3',
        'xl:grid-cols-4',
        'lg:col-span-1',
        'lg:col-span-3',
        'lg:col-span-4',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                red: {
                    50:  '#fdf9ef',
                    100: '#f9f0d5',
                    200: '#f2dda5',
                    300: '#e6c66b',
                    400: '#d9b355',
                    500: '#C9A84C',
                    600: '#C9A84C',
                    700: '#a88a30',
                    800: '#8a6f28',
                    900: '#725c24',
                    950: '#3e3011',
                },
            },
        },
    },

    plugins: [forms],
};

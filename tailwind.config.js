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
        },
    },

    plugins: [forms],
};

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
                // Remap red → gold (all existing red-* classes become gold)
                red: {
                    50:  '#FFF9EB',
                    100: '#FEF0CD',
                    200: '#FDDFA0',
                    300: '#E8C67A',
                    400: '#DDB55C',
                    500: '#D4A54A',
                    600: '#D4A54A',
                    700: '#B8892E',
                    800: '#96701F',
                    900: '#6B4F14',
                    950: '#4A360D',
                },
                // Actual red for errors, validation, danger states
                danger: {
                    50:  '#FEF2F2',
                    100: '#FEE2E2',
                    200: '#FECACA',
                    300: '#FCA5A5',
                    400: '#F87171',
                    500: '#EF4444',
                    600: '#DC2626',
                    700: '#B91C1C',
                    800: '#991B1B',
                    900: '#7F1D1D',
                },
                // Brand gold (alias for new code)
                gold: {
                    50:  '#FFF9EB',
                    100: '#FEF0CD',
                    200: '#FDDFA0',
                    300: '#E8C67A',
                    400: '#DDB55C',
                    500: '#D4A54A',
                    600: '#D4A54A',
                    700: '#B8892E',
                    800: '#96701F',
                    900: '#6B4F14',
                },
                // Mexican flag colors
                'mx-green': '#006847',
                'mx-red':   '#CE1126',
                // Yelp brand
                'yelp':     '#AF0606',
            },
        },
    },

    plugins: [forms],
};

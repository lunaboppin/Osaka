import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                osaka: {
                    red: '#C41E3A',
                    'red-dark': '#A01830',
                    gold: '#D4A843',
                    'gold-light': '#E8C97A',
                    charcoal: '#2D2D2D',
                    'charcoal-light': '#3D3D3D',
                    cream: '#FAF7F0',
                    'cream-dark': '#F0EBE0',
                    green: '#4A7C59',
                    'green-light': '#5A9C6E',
                    slate: '#64748B',
                },
            },
        },
    },

    plugins: [forms],
};

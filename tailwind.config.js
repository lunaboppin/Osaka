import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './config/osaka.php',
    ],

    safelist: [
        // Profile theme gradient classes (used dynamically from osaka.php config)
        'bg-gradient-to-r',
        'from-osaka-charcoal', 'to-osaka-charcoal-light',
        'from-violet-950', 'via-indigo-950', 'to-slate-900',
        'from-pink-200', 'via-rose-200', 'to-pink-300',
        'from-gray-900', 'via-cyan-950',
        'from-orange-950', 'via-red-950', 'to-amber-950',
        'from-sky-100', 'via-blue-100', 'to-indigo-100',
        'from-amber-500', 'via-yellow-400', 'to-amber-500',
        // Profile theme text colours
        'text-osaka-cream', 'text-violet-100', 'text-pink-900',
        'text-cyan-300', 'text-orange-200', 'text-sky-900', 'text-amber-950',
        // Profile theme card border classes
        'border-osaka-gold/20', 'border-violet-400/30', 'border-pink-300/40',
        'border-cyan-400/30', 'border-orange-400/30', 'border-sky-300/40', 'border-amber-400/50',
        // Avatar frame classes
        'border-amber-600', 'border-gray-400', 'border-osaka-gold', 'border-cyan-400',
        'ring-2', 'ring-offset-2', 'ring-offset-white',
        'ring-amber-600/30', 'ring-gray-400/30', 'ring-osaka-gold/40', 'ring-cyan-400/40', 'ring-purple-400/40',
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

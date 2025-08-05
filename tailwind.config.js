import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/preline/dist/*.js', // Add this line
    ],
    safelist: [
        'bg-blue-900',
        'bg-yellow-900',
        'bg-purple-900',
        'bg-green-900',
        'text-blue-100',
        'text-yellow-100',
        'text-purple-100',
        'text-green-100',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        require('preline/plugin'), // Add this line
    ],
};

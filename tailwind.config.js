import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        "./node_modules/flowbite/**/*.js",
        "./node_modules/flowbite-vue/**/*.{js,ts}",
    ],

    theme: {
        extend: {
            colors: {
                // Основная палитра
                brand: '#798D96',
                'brand-dark': '#292A25',
                accent: '#7A7658',
                'accent-light': '#8A8568',
                hover: '#4B504A',

                // Фоны
                page: '#C7C6B9',
                surface: '#F8F7F4',
                light: '#D8D7CE',
                muted: '#E4E3DC',

                // Текст
                text: '#292A25',
                'text-muted': '#5A5C56',
                'text-soft': '#7A7C76',

                // Границы
                border: '#A8A79E',
                divider: '#C4C3BA',

                // Состояния
                error: '#B42318',
                success: '#5A7A58',
                info: '#798D96',

                // Белый
                white: '#FFFFFF',
            },

            fontFamily: {
                brand: ['Lighthaus', 'Oranienbaum', 'Georgia', 'serif'],
                heading: ['Oranienbaum', 'Georgia', 'serif'],
                body: ['CalvinoVar', 'Georgia', 'Times New Roman', 'serif'],
            },

            borderRadius: {
                'sm': '0.375rem',
                'md': '0.5rem',
                'lg': '0.75rem',
                'xl': '1rem',
                '2xl': '1.5rem',
            },

            boxShadow: {
                'sm': '0 1px 3px rgba(41, 42, 37, 0.06)',
                'md': '0 6px 16px -8px rgba(41, 42, 37, 0.16)',
                'lg': '0 16px 34px -18px rgba(41, 42, 37, 0.22)',
                'xl': '0 28px 60px -30px rgba(41, 42, 37, 0.28)',
            },
        },
    },

    plugins: [forms, require('flowbite/plugin')],
};
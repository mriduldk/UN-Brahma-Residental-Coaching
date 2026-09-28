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
            colors: {
                primary: {
                    DEFAULT: '#2563eb', // blue-600
                    hover: '#1d4ed8',   // blue-700
                    light: '#dbeafe',   // blue-100
                },
                secondary: {
                    DEFAULT: '#475569', // slate-600
                    hover: '#334155',   // slate-700
                    light: '#f1f5f9',   // slate-100
                },
                success: {
                    DEFAULT: '#059669', // emerald-600
                    hover: '#047857',   // emerald-700
                    light: '#d1fae5',   // emerald-100
                },
                danger: {
                    DEFAULT: '#e11d48', // rose-600
                    hover: '#be123c',   // rose-700
                    light: '#ffe4e6',   // rose-100
                },
                warning: {
                    DEFAULT: '#d97706', // amber-600
                    hover: '#b45309',   // amber-700
                    light: '#fef3c7',   // amber-100
                },
                info: {
                    DEFAULT: '#4f46e5', // indigo-600
                    hover: '#4338ca',   // indigo-700
                    light: '#e0e7ff',   // indigo-100
                },
                background: '#f8fafc',  // slate-50
                surface: '#ffffff',
                border: '#e2e8f0',      // slate-200
                muted: '#64748b',       // slate-500
                foreground: '#0f172a',  // slate-900
            },
            borderRadius: {
                '4xl': '2rem',
                '3xl': '1.5rem',
                '2xl': '1rem',
                xl: '0.75rem',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

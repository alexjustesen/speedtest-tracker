import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import presets from './vendor/filament/support/tailwind.config.preset';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    presets: [presets],

    content: [
        // Core
        './resources/views/**/*.blade.php',

        // Filament
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        typography,
    ],
}

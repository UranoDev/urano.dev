import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                frost: {
                    dark: '#111111',
                    light: '#FAFAFA',
                    muted: '#666666',
                    border: '#E5E5E5'
                }
            },
            spacing: {
                'fluid-sm': 'clamp(1rem, 2vw, 1.5rem)',
                'fluid-md': 'clamp(2rem, 4vw, 4rem)',
                'fluid-lg': 'clamp(4rem, 8vw, 8rem)',
            }
        },
    },
    plugins: [],
};
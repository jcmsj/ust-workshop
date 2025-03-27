import preset from './vendor/filament/filament/tailwind.config.preset';
import daisyui from "daisyui"

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',
    ],
    darkMode: 'class',
    daisyui: {
        themes: [
            {
                hipandvalley: {
                    // primary: "#0078d7"
                    ...require("daisyui/src/theming/themes")["light"],
                    primary: "#023b73",
                    // primary: "#1d83ff",
                    "primary-content": "#faf6e9",
                    // primary: "#6bbcff",
                    // secondary: "#023b73",
                    // secondary:"#f56e00",
                    // secondary: "#ffeee0",
                    secondary: "#f7ebc6",
                    // secondary: "#ffca9e",
                    
                    // secondary: "#6CCFF6",
                    // 4th #e0f1ff 
                    // 5th #9ed3ff
                    accent: "#98CE00",
                    // neutral: "DFE3EE",
                    // "base-100": "#F7F7F7",
                    // "base-200": "#c3c3c3",
                    // "base-300":"#909090",
                    info: "#0000ff",
                    success: "#22c55e",
                    "success-content": "#faf6e9",
                    warning: "#fcd34d",
                    error: "#f43f5e",
                },
            },
            'dark',
        ],
    },

    plugins: [
        daisyui,
    ],
};

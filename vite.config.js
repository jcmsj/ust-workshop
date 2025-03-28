import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/app/theme.css', 'resources/css/filament/admin/theme.css', 
                // I've not made this file yet, but if i add more extensions that need css, I'll add them here
                // 'resources/css/tiptap/extensions.css',
            ],
            refresh: [
                ...refreshPaths,
                'app/Filament/**',
                'app/Forms/Components/**',
                'app/Livewire/**',
                'app/Infolists/Components/**',
                'app/Providers/Filament/**',
                'app/Tables/Columns/**',
            ],
        }),
    ],
    // server: {
    //     hmr: {
    //         host: '192.168.254.102',
    //     },
    // },
})

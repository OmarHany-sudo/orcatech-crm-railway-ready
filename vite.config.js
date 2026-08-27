import { defineConfig, loadEnv } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')
    const assetUrl = env.ASSET_URL || ''
    const base = assetUrl ? `${assetUrl.replace(/\/$/, '')}/` : '/'

    return {
        base,
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/css/filament/admin/theme.css',
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
            viteStaticCopy({
                targets: [
                    {
                        src: 'resources/images/*',
                        dest: 'images',
                    },
                ],
            }),
        ],
    }
})

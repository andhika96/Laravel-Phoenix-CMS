import { defineConfig } from 'vite';
import { mkdirSync, writeFileSync } from 'node:fs';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'node:path';

const source = resolve(__dirname, 'resources/js/filemanager_v2/main.js');
const storageRoot = resolve(__dirname, 'storage/app/public/filemanager_v2');

function protectFileManagerV2Storage() {
    return {
        name: 'filemanager-v2-storage-guard',
        writeBundle() {
            mkdirSync(storageRoot, { recursive: true });
            writeFileSync(resolve(storageRoot, '.htaccess'), `Options -Indexes
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Deny from all
</IfModule>
`);
        },
    };
}

export default defineConfig({
    base: '/assets/plugins/filemanager_v2/',
    publicDir: false,
    plugins: [vue(), protectFileManagerV2Storage()],
    build: {
        outDir: resolve(__dirname, 'public/assets/plugins/filemanager_v2'),
        emptyOutDir: true,
        cssCodeSplit: true,
        rollupOptions: {
            input: source,
            output: {
                entryFileNames: 'filemanager-v2.js',
                chunkFileNames: 'chunks/[name]-[hash].js',
                assetFileNames: (asset) => asset.name?.endsWith('.css')
                    ? 'filemanager-v2.css'
                    : 'assets/[name]-[hash][extname]',
            },
        },
    },
});

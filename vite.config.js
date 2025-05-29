import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        // Increase chunk size warning limit to reduce noise during dev
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                // Automatic vendor chunk splitting - no manual maintenance needed!
                manualChunks(id) {
                    // Split node_modules into vendor chunks automatically
                    if (id.includes('node_modules')) {
                        // Extract vuetify into its own chunk
                        if (id.includes('vuetify')) {
                            return 'vendor-vuetify';
                        }
                        // Extract vue ecosystem
                        if (id.includes('vue') || id.includes('@inertiajs')) {
                            return 'vendor-vue';
                        }
                        // Extract icon libraries
                        if (id.includes('@mdi')) {
                            return 'vendor-icons';
                        }
                        // Everything else from node_modules
                        return 'vendor';
                    }
                },
            },
        },
    },
});
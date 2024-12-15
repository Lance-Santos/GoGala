import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import commonjs from 'vite-plugin-commonjs';
import vitePluginRequire from 'vite-plugin-require';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css','resources/sass/app.scss', 'resources/js/app.js', 'resources/ts/app.tsx'],
            refresh: true,
        }),
        commonjs(),
        vitePluginRequire.default()
    ],
     build: {
    commonjsOptions: { transformMixedEsModules: true } // Change
  }
})

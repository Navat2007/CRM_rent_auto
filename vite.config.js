import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue';
import svgLoader from 'vite-svg-loader';
import Components from 'unplugin-vue-components/vite';
import {PrimeVueResolver} from '@primevue/auto-import-resolver';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig(({mode}) => ({
    base: './',
    plugins: [
        vue(),
        svgLoader(),
        Components({
            resolvers: [
                PrimeVueResolver()
            ]
        })
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './src'),
            '@components': path.resolve(__dirname, './src/components'),
            '@assets': path.resolve(__dirname, './src/assets'),
            '@layouts': path.resolve(__dirname, './src/layouts'),
            '@pages': path.resolve(__dirname, './src/pages'),
            '@stores': path.resolve(__dirname, './src/store'),
            '@utils': path.resolve(__dirname, './src/utils'),
            '@router': path.resolve(__dirname, './src/router'),
            '@services': path.resolve(__dirname, './src/services'),
        }
    },
    define: {
        __VUE_PROD_DEVTOOLS__: mode !== 'production'
    },
}))

import { splitVendorChunkPlugin } from 'vite'
import vue from '@vitejs/plugin-vue'

export default {
  base: './',
  build: {
    target: 'esnext',
    manifest: true,
    rollupOptions: {
      external: ['vue'],
      output: {
        format: 'umd',
        globals: {
          vue: 'Vue'
        },
        chunkFileNames: '[name].[hash].js',
        entryFileNames: '[name].[hash].js',
        assetFileNames: '[name].[hash][extname]',
      },
      input: [
        './src/js/mf.js'
      ],
    },
  },
  plugins: [
    splitVendorChunkPlugin(),
    vue()
  ],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js'
    },
    extensions: ['.js', '.vue', '.json']
  }
}

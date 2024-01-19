import { splitVendorChunkPlugin } from 'vite'
import vue from '@vitejs/plugin-vue'

export default {
  base: './',
  build: {
    target: 'esnext',
    manifest: true,
    rollupOptions: {
      output: {
        format: 'module',
        chunkFileNames: '[name].[hash].js',
        entryFileNames: '[name].[hash].js',
        assetFileNames: '[name].[hash][extname]',
      },
      input: [
        './src/js/mf.js'
      ]
    },
  },
  plugins: [
    splitVendorChunkPlugin(),
    vue()
  ],
  resolve: {
    alias: {
      '@': '/src/js/'
    },
    extensions: ['.js', '.vue', '.json']
  }
}

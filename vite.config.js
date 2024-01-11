import { splitVendorChunkPlugin } from 'vite'
import vue from '@vitejs/plugin-vue'

export default {
  base: './',
  build: {
    target: 'esnext'
  },
  plugins: [
    splitVendorChunkPlugin(),
    vue()
  ],
  resolve: {
    alias: {
      '@/': '/src/js/'
    },
    extensions: ['.js', '.vue', '.json']
  }
}

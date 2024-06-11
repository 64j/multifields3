import { splitVendorChunkPlugin } from 'vite'
import vue from '@vitejs/plugin-vue'
import fs from 'fs'

const hotFile = 'hot'
let exitHandlersBound = false

function isIpv6 (address) {
  return address.family === 'IPv6' || address.family === 6
}

function resolveDevServerUrl (address, config) {
  const configHmrProtocol = typeof config.server.hmr === 'object' ? config.server.hmr.protocol : null
  const clientProtocol = configHmrProtocol ? configHmrProtocol === 'wss' ? 'https' : 'http' : null
  const serverProtocol = config.server.https ? 'https' : 'http'
  const protocol = clientProtocol ?? serverProtocol
  const configHmrHost = typeof config.server.hmr === 'object' ? config.server.hmr.host : null
  const configHost = typeof config.server.host === 'string' ? config.server.host : null
  const serverAddress = isIpv6(address) ? `[${address.address}]` : address.address
  const host = configHmrHost ?? configHost ?? serverAddress
  const configHmrClientPort = typeof config.server.hmr === 'object' ? config.server.hmr.clientPort : null
  const port = configHmrClientPort ?? address.port
  return `${protocol}://${host}:${port}`
}

export default {
  base: './',
  build: {
    target: 'esnext',
    manifest: true,
    rollupOptions: {
      external: ['vue'],
      output: {
        //dir: './dist/mf',
        format: 'umd',
        globals: {
          vue: 'Vue'
        },
        chunkFileNames: '[name].js',
        entryFileNames: '[name].js',
        assetFileNames: '[name].[extname]'
      },
      // input: [
      //   './src/js/mf.js'
      // ]
    }
  },
  plugins: [
    splitVendorChunkPlugin(),
    vue(),
    {
      configureServer (server) {
        server.httpServer?.once('listening', () => {
          const address = server.httpServer?.address()
          const isAddressInfo = (x) => typeof x === 'object'
          if (isAddressInfo(address)) {
            fs.writeFileSync(hotFile, resolveDevServerUrl(address, server.config))
          }
        })

        if (!exitHandlersBound) {
          const clean = () => {
            if (fs.existsSync(hotFile)) {
              fs.rmSync(hotFile)
            }
          }
          process.on('exit', clean)
          process.on('SIGINT', process.exit)
          process.on('SIGTERM', process.exit)
          process.on('SIGHUP', process.exit)
          exitHandlersBound = true
        }
      }
    }
  ],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js'
    },
    extensions: ['.js', '.vue', '.json']
  }
}

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src')
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5174,
    allowedHosts: ['tiktokbusines.store', '.store'],
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:3000',
        changeOrigin: true,
        secure: false
      }
    },
    middlewareMode: false,
    fs: {
      allow: ['..']
    }
  },
  preview: {
    host: '0.0.0.0',
    port: 5174,
    allowedHosts: ['tiktokbusines.store', '.store'],
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:3000',
        changeOrigin: true,
        secure: false
      }
    }
  },
  base: '/merchant/',
  build: {
    outDir: 'dist',
    assetsDir: 'assets'
  }
})

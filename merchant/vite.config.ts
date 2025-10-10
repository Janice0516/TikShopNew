import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src')
    }
  },
  server: {
    port: 5176,
    host: '0.0.0.0',
    open: true,
    allowedHosts: [
      'localhost',
      '127.0.0.1',
      '192.168.0.121',
      'tiktokshop-merchant.loca.lt',
      '.loca.lt'
    ],
    proxy: {
      '/api': {
        target: 'https://tiktokshop-api.loca.lt',
        changeOrigin: true,
        secure: true
      }
    }
  }
})

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  base: '/admin/',
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src')
    }
  },
  define: {
    'import.meta.env.VITE_API_BASE_URL': JSON.stringify(process.env.NODE_ENV === 'production' ? 'https://tiktokbusines.store/api' : '/api')
  },
  server: {
    port: 5177,
    host: '0.0.0.0',
    open: true,
    allowedHosts: [
      'localhost',
      '127.0.0.1',
      '192.168.0.121',
      'tiktokshop-admin.loca.lt',
      '.loca.lt'
    ],
    proxy: {
      '/api': {
        target: process.env.NODE_ENV === 'production' ? 'https://tiktokbusines.store' : 'http://localhost:3000',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/api/, '/api')
      },
      '/upload': {
        target: process.env.NODE_ENV === 'production' ? 'https://tiktokbusines.store' : 'http://localhost:3000',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/upload/, '/api/upload')
      }
    }
  }
})

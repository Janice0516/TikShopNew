import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  base: '/admin/',
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
    },
  },
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
  },
  server: {
    port: 4173,
    host: '0.0.0.0',
    strictPort: true,
  },
  preview: {
    port: 4173,
    host: '0.0.0.0',
    strictPort: true,
  },
})

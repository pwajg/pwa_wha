import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
        secure: false,
      }
    }
  },
  optimizeDeps: {
    include: ['xlsx', 'jspdf', 'html2canvas'],
    esbuildOptions: {
      supported: {
        'top-level-await': true
      }
    }
  },
  build: {
    commonjsOptions: {
      include: [/xlsx/, /node_modules/]
    }
  }
})

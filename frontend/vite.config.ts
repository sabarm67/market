import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), tailwindcss()],
  server: {
    port: 5173,
  },
  // Production build is emitted straight into the Laravel app's public dir so one
  // Forge site (backend/public as web root) serves both the SPA and the API — see
  // backend/routes/web.php for the catch-all route that serves this build's index.html.
  base: '/app/',
  build: {
    outDir: '../backend/public/app',
    emptyOutDir: true,
  },
})

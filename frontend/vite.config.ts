import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), tailwindcss()],
  server: {
    port: 5173,
  },
  // Production build is emitted straight into the Laravel app's public dir (Laravel
  // lives at the repo root — see ADR-0006) so one Forge site (public/ as web root)
  // serves both the SPA and the API — see ../routes/web.php for the catch-all route
  // that serves this build's index.html.
  base: '/app/',
  build: {
    outDir: '../public/app',
    emptyOutDir: true,
  },
})

import { defineConfig } from 'vite';

export default defineConfig({
  server: {
    host: '0.0.0.0',
    port: 5173,
    hmr: true,
  },
  build: {
    manifest: true,
    outDir: './test/build',
    rollupOptions: {
      input: [
        'asset/js/script.js',
        'asset/scss/style.scss',
      ],
    },
  },
});

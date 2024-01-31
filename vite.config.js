import { defineConfig } from 'vite';

export default defineConfig({
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

import { v4wp } from '@kucrut/vite-for-wp';

export default {
  plugins: [
    v4wp( {
      input: 'assets/js/main.js',
      outDir: 'assets/js/dist',
    } ),
  ],
};
import animate from 'tailwindcss-animate';

export default {
  content: [
    "./resources/**/*.{vue,js,jsx,ts,tsx}",
    "./resources/views/**/*.blade.php",
  ],
  theme: {
    extend: {
      animation: {},
    },
  },
  plugins: [animate],
}
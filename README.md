<h3>Add tailwind:</h3><br/>
pnpm add -D tailwindcss@latest postcss@latest autoprefixer@latest <br/>
pnpm add -D sass <br/>
npx tailwindcss init -p

add to content in tailwind.config.js:
content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],

add to style.css:
@tailwind base;
@tailwind components;
@tailwind utilities;

<h3>Libraries:</h3><br/>
pnpm add
axios
lodash
pinia
vue-hooks-form
vue-router
vite-svg-loader

pnpm add primevue @primevue/themes

<h3>References:</h3><br/>
https://tailwind.primevue.org<br/>
https://flowbite.com
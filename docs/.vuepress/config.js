module.exports = {
  title: "Vuepress Blog Example",
  description: "just another blog",
  base: '',
  postcss: {
    plugins: [
      require("tailwindcss")("./tailwind.config.js"),
      require("autoprefixer"),
    ],
  },
}

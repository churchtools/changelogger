module.exports = {
  title: "Changelogger Website",
  description: "Log Your Changes; Developer friendly",
  base: "/changelogger/",
  postcss: {
    plugins: [
      require("tailwindcss")("./tailwind.config.js"),
      require("autoprefixer"),
    ],
  },
  themeConfig: {
    nav: [
      { text: 'Home', link: '/changelogger/', icon: 'home' },
      { text: 'Changelog', link: '/changelogger/changelog.html', icon: 'announcement' },
      { text: 'GitHub', link: 'https://github.com/churchtools/changelogger', icon: 'code' }
    ]
  },
  markdown: {
    anchor: { permalink: false }
  }
}

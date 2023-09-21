(window.webpackJsonp=window.webpackJsonp||[]).push([[8],{244:function(e,a,t){"use strict";t.r(a);var s=t(14),n=Object(s.a)({},(function(){var e=this,a=e._self._c;return a("ContentSlotsDistributor",{attrs:{"slot-key":e.$parent.slotKey}},[a("h1",{attrs:{id:"changelogger"}},[e._v("Changelogger")]),e._v(" "),a("p",[a("strong",[e._v("Changeloggger is a CLI tool to help you create better changelogs")])]),e._v(" "),a("p",[e._v("At ChurchTools we develop a SaaS. To keep our users informed, we write changelogs for every version. We used to add our changelogs to the issue, but many times we forgot to add the changelog—and manual copy 'n' paste is tedious work! This tool helps us to write and create consistent changelogs that are committable so the reviewer can check it before merging.")]),e._v(" "),a("hr"),e._v(" "),a("h2",{attrs:{id:"what-does-it-do"}},[e._v("What does it do?")]),e._v(" "),a("p",[a("em",[e._v("Changelogger")]),e._v(" saves each log entry as a YAML file in "),a("code",[e._v("changelogs/unreleased")]),e._v(". During the development process many files from different features, bug fixes, and so on find their way into this folder.")]),e._v(" "),a("p",[e._v("We create for each change a different file to easily track them. Adding them to a single file, like "),a("code",[e._v("Changelog.md")]),e._v(" directly could lead to nasty merge conflicts. One file per change avoids that.")]),e._v(" "),a("p",[e._v("In the end, if a new version is built, "),a("em",[e._v("Changelogger")]),e._v(" takes all unreleased changes, sorts them and adds a new release to "),a("code",[e._v("CHANGELOG.md")]),e._v(". After that all files in "),a("code",[e._v("changelogs/unreleased")]),e._v(" are deleted and your project is ready for the next version.")]),e._v(" "),a("p",[a("strong",[e._v("Changelogger is not taking your git-logs.")]),e._v(" There exists many tools that take your git-log and converts commit messages to changelog entries. IMHO, this leads in many cases to bad changelogs. Commit messages are not changelogs. They are pieces of information for developers and not the users. Our changelogs are handcrafted because non-developers, AKA our users, should be able to understand the changes.")]),e._v(" "),a("h2",{attrs:{id:"demo"}},[e._v("Demo")]),e._v(" "),a("p",[a("img",{attrs:{src:"https://raw.githubusercontent.com/churchtools/changelogger/master/assets/changelogger-demo.gif",alt:"Changelogger Demo"}})]),e._v(" "),a("h2",{attrs:{id:"installation"}},[e._v("Installation")]),e._v(" "),a("p",[e._v("This package requires PHP 7.1.3.\nYou can require the package as a dev-dependency")]),e._v(" "),a("div",{staticClass:"language-bash extra-class"},[a("pre",{pre:!0,attrs:{class:"language-bash"}},[a("code",[a("span",{pre:!0,attrs:{class:"token function"}},[e._v("composer")]),e._v(" require "),a("span",{pre:!0,attrs:{class:"token parameter variable"}},[e._v("--dev")]),e._v(" churchtools/changelogger\n")])])]),a("p",[e._v("or install it globally.")]),e._v(" "),a("div",{staticClass:"language-bash extra-class"},[a("pre",{pre:!0,attrs:{class:"language-bash"}},[a("code",[a("span",{pre:!0,attrs:{class:"token function"}},[e._v("composer")]),e._v(" global require churchtools/changelogger\n")])])]),a("h2",{attrs:{id:"usage"}},[e._v("Usage")]),e._v(" "),a("div",{staticClass:"language-bash extra-class"},[a("pre",{pre:!0,attrs:{class:"language-bash"}},[a("code",[a("span",{pre:!0,attrs:{class:"token comment"}},[e._v("# To add a new changelog use `new`.")]),e._v("\nchangelogger new\n\n"),a("span",{pre:!0,attrs:{class:"token comment"}},[e._v("# When a new version is released run `release` to generate the changelog.")]),e._v("\n"),a("span",{pre:!0,attrs:{class:"token comment"}},[e._v("# The <tag> is the version number or build number of the release.")]),e._v("\nchangelogger release "),a("span",{pre:!0,attrs:{class:"token operator"}},[e._v("<")]),e._v("tag"),a("span",{pre:!0,attrs:{class:"token operator"}},[e._v(">")]),e._v("\n\n"),a("span",{pre:!0,attrs:{class:"token comment"}},[e._v("# Need to start over? Run `clean` to remove all unreleased logs.")]),e._v("\nchangelogger clean\n")])])]),a("h2",{attrs:{id:"license"}},[e._v("License")]),e._v(" "),a("p",[a("a",{attrs:{href:"https://github.com/churchtools/changelogger",target:"_blank",rel:"noopener noreferrer"}},[e._v("churchtools/changelogger"),a("OutboundLink")],1),e._v(" is licensed under the\n"),a("a",{attrs:{href:"LICENSE"}},[e._v("Apache License 2.0")])])])}),[],!1,null,null,null);a.default=n.exports}}]);
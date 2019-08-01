# Changeloger

![Changelogger Banner](./assets/banner.png)

<p align="center">
  <a href="https://packagist.org/packages/churchtools/changelogger"><img src="https://poser.pugx.org/churchtools/changelogger/d/total.svg?format=flat-square" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/churchtools/changelogger"><img src="https://poser.pugx.org/churchtools/changelogger/v/stable.svg?format=flat-square" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/churchtools/changelogger"><img src="https://poser.pugx.org/churchtools/changelogger/license.svg?format=flat-square" alt="License"></a>
</p>

**Changelogger is a CLI tool to help you creating better changelogs**

At ChurchTools we develop a SaaS. To keep our users informed, we write changelogs for every version. We used to add our changelogs to the issue, but many times the changelog is forgotten to add and manually copy'n'paste is a tedious work. This tool helps us to write and create consistent changelogs, which are committable so the reviewer can check it before merging.

---

## Installation

This package requires PHP 7.1.3.
You can require the package as dev-dependency

```bash
composer require --dev churchtools/changelogger
```

or install it globally.

```bash
composer global require churchtools/changelogger
```

## Usage

```bash
# To add a new changelog use `add`
changelogger add

# When a new version is release run `build` to generate the changelog.
# The <tag> is the version number or build number of the release.
changelogger build <tag>

# Need to start over? Run `clean` to remove all unreleased logs.
changelogger clean
```

## License

[churchtools/changelogger](https://github.com/churchtools/changelogger) is licensed under the
[Apache License 2.0](LICENSE)

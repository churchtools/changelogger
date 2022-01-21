---
layout: ChangelogLayout
---
# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

<!-- CHANGELOGGER -->

## [v0.7.0] - 2022-01-12

### Feature removal (1 change)

- Drop support for PHP 7.3

### New feature (1 change)

- Support PHP 8.0

### Security fix (1 change)

- Update npm packages with security patches (only used by website)

## [v0.6.0] - 2021-12-14

### New feature (4 changes)

- Update minimal PHP Version to 7.3
- Markdown list styles can now be configured.
- Adds a new config option groupsAsList.
- Using the release command without any changes does not change the CHANGELOG.md file anymore.


## [v0.5.0] - 2020-05-01

### Feature removal (1 change)

- Drop support for PHP 7.1

### Other (1 change)

- Update to Laravel Zero 7.1. Changelogger requires PHP 7.2.5.

### New feature (2 changes)

- Move changelogger type from Types.php to config/changelogger.php (props @cwandrey)
- Define custom types in .changelogger.yml


## [v0.4.0] - 2020-01-02

### Feature change (2 changes)

- BREAKING: Use YAML format for config file. Old JSON configs must be converted.
- If 'No Changelog' is chosen from the list, no further questions will be asked.


## [v0.3.0] - 2019-09-09

### Feature change (1 change)

- New command uses datetimes as filename prefixes as defaults.

### New feature (1 change)

- New show command to list all unreleased changes

### Bug fix (1 change)

- Using --empty flag for no changelogs, no question is asked for a group.


## [v0.2.0] - 2019-08-09

### Bug fix (1 change)

- Fix info command

### Feature change (2 changes)

- Rename command `build` to `release`
- Rename command `add` to `new`

### Other (3 changes)

- Add License to the project
- Write tests for logic and commands
- Add section 'What does it do?' to README, explaining the functionality on a high level

### New feature (3 changes)

- New `.changelogger.json` file for configuration
- Release command sorts logs by groups if groups are specified in config file
- Clean command has now a force flag


## [v0.1.1] - 2019-08-01

### Bug fix (1 change)

* Build and link to correct changelogger phar


## [v0.1.0] - 2019-08-01

### New feature (2 changes)

* Clean command, to delete all unreleased logs
* Allow empty log entries, which are ignored on build

### Other (1 change)

* Add project information to composer.json


## [v0.0.2] - 2019-07-31

### Feature change (1 change)

* New changelog entry is replaced by placholder


## [v0.0.1] - 2019-07-31

### New features (2 changes)

* Add command to create new changelogs
* Build command to generate changelog

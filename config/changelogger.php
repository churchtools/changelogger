<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Changelogger Directories
    |--------------------------------------------------------------------------
    |
    | Two config variables are set. One is the root directory. This directory
    | holds the CHANGELOG.md file. The other is the unreleased directory
    | where all unreleased log changes are saved to.
    |
    */
    'directory'  => env('DIRECTORY', getcwd()),
    'unreleased' => env('DIRECTORY', getcwd()) . '/changelogs/unreleased',
];

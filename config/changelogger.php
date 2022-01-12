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
    'types'      => [
            'New feature'             => 'added',
            'Bug fix'                 => 'fixed',
            'Hotfix'                  => 'hotfix',
            'Feature change'          => 'changed',
            'New deprecation'         => 'deprecated',
            'Feature removal'         => 'removed',
            'Security fix'            => 'security',
            'Performance improvement' => 'performance',
            'Other'                   => 'other',
            'No Changelog'            => 'ignore',
    ],
    'markdown'      => [
        'listStyle' => '-',
        'groupsAsList' => false,
    ],
];

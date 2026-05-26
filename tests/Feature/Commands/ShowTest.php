<?php

namespace Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ShowTest extends TestCase
{

    public function tearDown(): void
    {
        File::delete(config('changelogger.directory') . '/CHANGELOG.md');
        parent::tearDown();
    }


    public function testShowingUnreleasedChangesAsMarkdown() : void
    {
        $this->artisan('new',
            ['--type' => 'added', '--message' => 'Feature added', '--file' => 'file1'])
            ->assertExitCode(0);
        $this->artisan('new',
            ['--type' => 'fixed', '--message' => 'Bug fixed', '--file' => 'file2'])
            ->assertExitCode(0);

        $this->artisan('show')
            ->expectsOutputToContain(<<<CHANGE
### Bug fix (1 change)

- Bug fixed

### New feature (1 change)

- Feature added
CHANGE)
            ->assertExitCode(0);
    }


    public function testShowingSpecificReleasedVersion() : void
    {
        File::put(config('changelogger.directory') . '/CHANGELOG.md', <<<CHANGE
# Changelog

<!-- CHANGELOGGER -->

## [v1.1.0] - 2026-05-26

### New feature (1 change)

- Newer feature


## [v1.0.0] - 2026-05-25

### Bug fix (1 change)

- Bug fixed


## [v0.9.0] - 2026-05-24

### Other (1 change)

- Older change
CHANGE);

        $this->artisan('show', ['version' => 'v1.0.0'])
            ->expectsOutput(<<<CHANGE
## [v1.0.0] - 2026-05-25

### Bug fix (1 change)

- Bug fixed
CHANGE)
            ->assertExitCode(0);
    }


    public function testShowingMissingReleasedVersionFails() : void
    {
        File::put(config('changelogger.directory') . '/CHANGELOG.md', <<<CHANGE
# Changelog

<!-- CHANGELOGGER -->

## [v1.0.0] - 2026-05-25

### Bug fix (1 change)

- Bug fixed
CHANGE);

        $this->artisan('show', ['version' => 'v1.0'])
            ->expectsOutput('Error: Release "v1.0" was not found in CHANGELOG.md.')
            ->assertExitCode(1);
    }
}

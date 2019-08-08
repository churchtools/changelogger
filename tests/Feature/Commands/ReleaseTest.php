<?php

namespace Tests\Feature\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ReleaseTest extends TestCase
{

    public function testBuildingChangelogWith2Logs() : void
    {
        $this->artisan('new',
            ['--type' => 'added', '--message' => 'Feature 1 added', '--file' => 'file1'])
            ->assertExitCode(0);
        $this->artisan('new',
            ['--type' => 'added', '--message' => 'Feature 2 added', '--file' => 'file2'])
            ->assertExitCode(0);
        $this->artisan('new',
            ['--type' => 'fixed', '--message' => 'Bug fixed', '--file' => 'file3'])
            ->assertExitCode(0);

        $this->assertFileExists(config('changelogger.unreleased') . '/file1.yml');
        $this->assertFileExists(config('changelogger.unreleased') . '/file2.yml');
        $this->assertFileExists(config('changelogger.unreleased') . '/file3.yml');

        $this->artisan('release', ['tag' => 'v1.0.0'])
            ->expectsOutput('Changelog for v1.0.0 created')
            ->assertExitCode(0);

        $this->assertCommandCalled('release', ['tag' => 'v1.0.0']);
        $this->assertFileNotExists(config('changelogger.unreleased') . '/file1.yml');
        $this->assertFileNotExists(config('changelogger.unreleased') . '/file2.yml');
        $this->assertFileNotExists(config('changelogger.unreleased') . '/file3.yml');
        $this->assertFileExists(config('changelogger.directory') . '/CHANGELOG.md');

        $today = Carbon::now()->format('Y-m-d');
        $changelog = <<<CHANGE
<!-- CHANGELOGGER -->

## [v1.0.0] - {$today}

### Bug fix (1 change)

- Bug fixed

### New feature (2 changes)

- Feature 1 added
- Feature 2 added

CHANGE;

        $this->assertEquals(
            $changelog,
            File::get(config('changelogger.directory') . '/CHANGELOG.md')
        );
    }

    public function testBuildingEmptyChangelog() : void
    {
        File::delete(config('changelogger.directory') . '/CHANGELOG.md');
        $this->artisan('release', ['tag' => 'v1.0.0'])
            ->expectsOutput('Changelog for v1.0.0 created')
            ->assertExitCode(0);

        $this->assertCommandCalled('release', ['tag' => 'v1.0.0']);
        $this->assertFileExists(config('changelogger.directory') . '/CHANGELOG.md');

        $today = Carbon::now()->format('Y-m-d');
        $changelog = <<<CHANGE
<!-- CHANGELOGGER -->

## [v1.0.0] - {$today}

No changes.
CHANGE;

        $this->assertEquals(
            $changelog,
            File::get(config('changelogger.directory') . '/CHANGELOG.md')
        );
    }
}

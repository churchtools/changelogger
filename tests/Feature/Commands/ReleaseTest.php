<?php

namespace Tests\Feature\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class ReleaseTest extends TestCase
{

    public function tearDown(): void
    {
        File::delete(config('changelogger.directory') . '/CHANGELOG.md');
    }

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
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file1.yml');
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file2.yml');
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file3.yml');
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

    public function testBuildingChangelogWith2LogsAndGroups() : void
    {
        File::put(config('changelogger.directory') . '/.changelogger.yml', Yaml::dump(['groups' => ['Calendar', 'Wiki']]));
        $this->refreshApplication();

        $this->artisan('new',
            ['--type' => 'added', '--message' => 'Feature 1 added', '--file' => 'file1', '--group' => 'Wiki'])
            ->assertExitCode(0);
        $this->artisan('new',
            ['--type' => 'added', '--message' => 'Feature 2 added', '--file' => 'file2', '--group' => 'Calendar'])
            ->assertExitCode(0);
        $this->artisan('new',
            ['--type' => 'fixed', '--message' => 'Bug fixed', '--file' => 'file3', '--group' => 'Calendar'])
            ->assertExitCode(0);

        $this->assertFileExists(config('changelogger.unreleased') . '/file1.yml');
        $this->assertFileExists(config('changelogger.unreleased') . '/file2.yml');
        $this->assertFileExists(config('changelogger.unreleased') . '/file3.yml');

        $this->artisan('release', ['tag' => 'v1.0.0'])
            ->expectsOutput('Changelog for v1.0.0 created')
            ->assertExitCode(0);

        $this->assertCommandCalled('release', ['tag' => 'v1.0.0']);
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file1.yml');
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file2.yml');
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file3.yml');
        $this->assertFileExists(config('changelogger.directory') . '/CHANGELOG.md');

        $today = Carbon::now()->format('Y-m-d');
        $changelog = <<<CHANGE
<!-- CHANGELOGGER -->

## [v1.0.0] - {$today}

### Bug fix (1 change)

#### Calendar

- Bug fixed

### New feature (2 changes)

#### Calendar

- Feature 2 added

#### Wiki

- Feature 1 added

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
            ->expectsOutput('No Changes -> No Changelog for v1.0.0 created')
            ->assertExitCode(0);

        $this->assertCommandCalled('release', ['tag' => 'v1.0.0']);
        $this->assertFileDoesNotExist(config('changelogger.directory') . '/CHANGELOG.md');
    }

    public function testBuildingChangelogWithMarkdownListStyleStar() : void
    {
        File::put(config('changelogger.directory') . '/.changelogger.yml', Yaml::dump(['groups' => ['Wiki'], 'markdown' => ['listStyle' => '*']]));
        $this->refreshApplication();
        $this->artisan('new',
            ['--type' => 'added', '--message' => 'Feature 1 added', '--file' => 'file1', '--group' => 'Wiki'])
            ->assertExitCode(0);

        $this->assertFileExists(config('changelogger.unreleased') . '/file1.yml');

        $this->artisan('release', ['tag' => 'v1.0.0'])
            ->expectsOutput('Changelog for v1.0.0 created')
            ->assertExitCode(0);

        $this->assertCommandCalled('release', ['tag' => 'v1.0.0']);
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file1.yml');
        $this->assertFileExists(config('changelogger.directory') . '/CHANGELOG.md');

        $today = Carbon::now()->format('Y-m-d');
        $changelog = <<<CHANGE
<!-- CHANGELOGGER -->

## [v1.0.0] - {$today}

### New feature (1 change)

#### Wiki

* Feature 1 added

CHANGE;

        $this->assertEquals(
            $changelog,
            File::get(config('changelogger.directory') . '/CHANGELOG.md')
        );
    }

    public function testBuildingChangelogWithMarkdownGroupsAsList() : void
    {
        File::put(config('changelogger.directory') . '/.changelogger.yml', Yaml::dump(['groups' => ['Wiki'], 'markdown' => ['groupsAsList' => true]]));
        $this->refreshApplication();
        $this->artisan('new',
            ['--type' => 'added', '--message' => 'Feature 1 added', '--file' => 'file1', '--group' => 'Wiki'])
            ->assertExitCode(0);

        $this->assertFileExists(config('changelogger.unreleased') . '/file1.yml');

        $this->artisan('release', ['tag' => 'v1.0.0'])
            ->expectsOutput('Changelog for v1.0.0 created')
            ->assertExitCode(0);

        $this->assertCommandCalled('release', ['tag' => 'v1.0.0']);
        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/file1.yml');
        $this->assertFileExists(config('changelogger.directory') . '/CHANGELOG.md');

        $today = Carbon::now()->format('Y-m-d');
        $changelog = <<<CHANGE
<!-- CHANGELOGGER -->

## [v1.0.0] - {$today}

### New feature (1 change)

- **Wiki**
  - Feature 1 added

CHANGE;

        $this->assertEquals(
            $changelog,
            File::get(config('changelogger.directory') . '/CHANGELOG.md')
        );
    }
}

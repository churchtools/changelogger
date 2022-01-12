<?php

namespace Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class NewChangelogTest extends TestCase
{

    public function testAddNewChangelog() : void
    {
        $expected = <<<EMPTY
title: 'Test log'
type: added
author: ''
group: ''

EMPTY;
        $this->artisan('new', ['--file' => 'newLog'])
            ->expectsQuestion('Type of change', 'New feature')
            ->expectsQuestion('Your changelog', 'Test log')
            ->expectsOutput('Changelog generated:')
            ->assertExitCode(0);

        $this->assertCommandCalled('new', ['--file' => 'newLog']);
        $log = config('changelogger.unreleased') . '/newLog.yml';
        $this->assertFileExists($log);
        $content = File::get($log);
        $this->assertEquals($expected, $content);
        File::delete($log);
    }


    public function testAddEmptyChangelog() : void
    {
        $expected = <<<EMPTY
title: 'No changelog necessary'
type: ignore
author: ''
group: ''

EMPTY;

        $this->artisan('new', ['--empty' => true, '--file' => 'empty'])
            ->expectsOutput('Changelog generated:')
            ->assertExitCode(0);

        $this->assertCommandCalled('new', ['--empty' => true, '--file' => 'empty']);
        $log = config('changelogger.unreleased') . '/empty.yml';
        $this->assertFileExists($log);
        $content = File::get($log);
        $this->assertEquals($expected, $content);
        File::delete($log);
    }


    public function testNewLogWithGroup() : void
    {
        $expected = <<<EMPTY
title: 'Test log'
type: added
author: ''
group: Calendar

EMPTY;

        $this->withGroups();

        $this->artisan('new', ['--file' => 'newLog'])
            ->expectsQuestion('Type of change', 'New feature')
            ->expectsQuestion('Group of change', 'Calendar')
            ->expectsQuestion('Your changelog', 'Test log')
            ->expectsOutput('Changelog generated:')
            ->assertExitCode(0);

        $this->assertCommandCalled('new', ['--file' => 'newLog']);
        $log = config('changelogger.unreleased') . '/newLog.yml';
        $this->assertFileExists($log);
        $content = File::get($log);
        $this->assertEquals($expected, $content);
        File::delete($log);
    }


    private function withGroups() : void
    {
        File::put(config('changelogger.directory') . '/.changelogger.yml',
            Yaml::dump(['groups' => ['Calendar']]));
        $this->refreshApplication();
    }


    public function testNewLogWithGroupViaOption() : void
    {
        $expected = <<<EMPTY
title: 'Test log'
type: added
author: ''
group: Calendar

EMPTY;

        $this->withGroups();

        $this->artisan('new', ['--file' => 'newLog', '--group' => 'Calendar'])
            ->expectsQuestion('Type of change', 'New feature')
            ->expectsQuestion('Your changelog', 'Test log')
            ->expectsOutput('Changelog generated:')
            ->assertExitCode(0);

        $this->assertCommandCalled('new', ['--file' => 'newLog', '--group' => 'Calendar']);
        $log = config('changelogger.unreleased') . '/newLog.yml';
        $this->assertFileExists($log);
        $content = File::get($log);
        $this->assertEquals($expected, $content);
        File::delete($log);
    }


    public function testTryAddNewLogWithInvalidGroup() : void
    {
        $this->withGroups();

        $this->artisan('new', ['--file' => 'newLog', '--group' => 'Invalid Group'])
            ->expectsQuestion('Type of change', 'New feature')
            ->expectsOutput('No valid group. Use one of the following: Calendar')
            ->assertExitCode(0);

        $this->assertCommandCalled('new', ['--file' => 'newLog', '--group' => 'Invalid Group']);
        $log = config('changelogger.unreleased') . '/newLog.yml';
        $this->assertFileDoesNotExist($log);
    }


    public function testTryAddNewLogWithGroupButNoGroupsAreInConfig() : void
    {
        File::put(config('changelogger.directory') . '/.changelogger.yml', Yaml::dump(['groups' => []]));
        $this->refreshApplication();

        $this->artisan('new', ['--file' => 'newLog', '--group' => 'Invalid Group'])
            ->expectsQuestion('Type of change', 'New feature')
            ->assertExitCode(0);

        $this->assertCommandCalled('new', ['--file' => 'newLog', '--group' => 'Invalid Group']);
        $log = config('changelogger.unreleased') . '/newLog.yml';
        $this->assertFileDoesNotExist($log);
    }


    public function testInvalidTypeExpectsException() : void
    {
        $this->withoutGroups();

        $this->artisan('new', ['--type' => 'invalid'])
            ->expectsOutput('No valid type. Use one of the following: added, fixed, hotfix, changed, deprecated, removed, security, performance, other, ignore')
            ->assertExitCode(0);
    }


    private function withoutGroups() : void
    {
        File::delete(config('changelogger.directory') . '/.changelogger.yml');
        $this->refreshApplication();
    }
}

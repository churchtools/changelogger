<?php

namespace Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class NewChangelogTest extends TestCase
{

    public function testAddNewChangelog() : void
    {
        $expected = <<<EMPTY
title: 'Test log'
type: added
author: ''

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


    public function testInvalidTypeExpectsException() : void
    {
        $this->artisan('new', ['--type' => 'invalid'])
            ->expectsOutput('No valid type. Use one of the following: added, fixed, changed, deprecated, removed, security, performance, other, ignore')
            ->assertExitCode(0);
    }
}

<?php

namespace Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class CleanTest
 * @package Tests\Feature\Commands
 * @covers \App\Commands\CleanCommand
 */
class CleanTest extends TestCase
{

    public function testAttemptToCleanNoChangesGivesInfo() : void
    {
        $this->assertDirectoryExists(config('changelogger.unreleased'));
        $this->artisan('clean')
            ->expectsOutput('No logs. Nothing to delete.')
            ->assertExitCode(0);
    }


    public function testCleanOneFileAndExpectCorrectInfo(): void
    {
        $this->assertDirectoryExists(config('changelogger.unreleased'));
        File::put(config('changelogger.unreleased') . '/test.yml', 'Test');
        $this->artisan('clean')
            ->expectsQuestion('Do you want to delete 1 file?', true)
            ->assertExitCode(0);

        $this->assertFileDoesNotExist(config('changelogger.unreleased') . '/test.yml');
    }
}

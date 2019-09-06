<?php

namespace Tests\Feature;

use App\ChangesDirectory;
use App\LogEntry;
use Tests\TestCase;

/**
 * Class ChangesDirectoryTest
 * @package Tests\Feature
 * @covers \App\ChangesDirectory
 */
class ChangesDirectoryTest extends TestCase
{

    /** @var ChangesDirectory */
    private $sut;

    /** @var string Temp changes directory */
    private $path;


    public function testChangesDirExistsAndIsWritable() : void
    {
        $this->assertDirectoryExists($this->path);
        $this->assertDirectoryIsWritable($this->path);
    }


    /**
     * @depends testChangesDirExistsAndIsWritable
     */
    public function testDirectoryHasNoChangesAfterInitializing() : void
    {
        $this->assertFalse($this->sut->hasChanges());
    }


    /**
     * @depends testDirectoryHasNoChangesAfterInitializing
     */
    public function testAddingLogEntryAndCheckIfChangesExists() : void
    {
        $this->sut->add(new LogEntry('Test Title', 'Test Type', 'Test Author'), 'testLog.yaml');
        $this->assertTrue($this->sut->hasChanges());
    }


    /**
     * @depends testAddingLogEntryAndCheckIfChangesExists
     */
    public function testUnreleasedDirectoryCanBeCleaned() : void
    {
        $this->sut->clean();
        $this->assertFalse($this->sut->hasChanges());
    }


    protected function setUp() : void
    {
        parent::setUp();

        $this->path = config('changelogger.unreleased');
        $this->sut  = new ChangesDirectory($this->path);
    }
}

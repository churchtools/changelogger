<?php

namespace Tests\Feature;

use App\LogEntry;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;

/**
 * Class LogEntryTest
 * @package Tests\Feature
 * @covers  \App\LogEntry
 */
class LogEntryTest extends TestCase
{

    /** @var LogEntry */
    private $sut;


    public function testLogEntryKeepsInformation() : void
    {
        $this->assertEquals('Test Title', $this->sut->title());
        $this->assertEquals('Test Type', $this->sut->type());
        $this->assertEquals('Test Author', $this->sut->author());
    }


    public function testLogEntryToArray() : void
    {
        $data = $this->sut->toArray();
        $this->assertEquals('Test Title', $data['title']);
        $this->assertEquals('Test Type', $data['type']);
        $this->assertEquals('Test Author', $data['author']);
    }


    public function testLogEntryToYaml() : void
    {
        $expected = <<<YAML
title: 'Test Title'
type: 'Test Type'
author: 'Test Author'
group: ''

YAML;

        $this->assertEquals($expected, $this->sut->toYaml());
    }


    public function testParseLogEntryFromYaml() : void
    {
        $yaml = <<<YAML
title: 'YAML File Title'
type: 'YAML File Type'
author: 'YAML File Author'

YAML;
        $file = \Mockery::mock(SplFileInfo::class);
        $file->allows(['getContents' => $yaml]);

        $this->sut = LogEntry::parse($file);
        $this->assertEquals('YAML File Title', $this->sut->title());
        $this->assertEquals('YAML File Type', $this->sut->type());
        $this->assertEquals('YAML File Author', $this->sut->author());
    }


    protected function setUp() : void
    {
        parent::setUp();

        $this->sut = new LogEntry('Test Title', 'Test Type', 'Test Author');
    }
}

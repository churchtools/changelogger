<?php

namespace Tests\Feature;

use App\ChangeloggerConfig;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ChangeloggerConfigTest extends TestCase
{

    protected function tearDown(): void
    {
        File::delete('./.changelogger.json');
        parent::tearDown();
    }

    public function testReturnDefaultEnStringIfNoConfigFileExists(): void
    {
        File::delete('./.changelogger.json');
        $config = new ChangeloggerConfig('.');

        $this->assertEquals('en', $config->getLanguage());
    }


    public function testReturnCustomLanguageSettingFromConfigFile(): void
    {
        File::put('./.changelogger.json', json_encode(['language' => 'de']));
        $config = new ChangeloggerConfig('.');

        $this->assertEquals('de', $config->getLanguage());
    }
}

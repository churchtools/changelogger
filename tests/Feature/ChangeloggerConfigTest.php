<?php

namespace Tests\Feature;

use App\ChangeloggerConfig;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ChangeloggerConfigTest extends TestCase
{

    public function groupProvider() : array
    {
        $expected = ['Wiki', 'Person', 'Report', 'Calendar', 'Checkin'];

        return [
            'same'    => [
                ['Wiki', 'Person', 'Report', 'Calendar', 'Checkin'],
                $expected,
            ],
            'reverse' => [
                ['Checkin', 'Calendar', 'Report', 'Person', 'Wiki'],
                $expected,
            ],
            'random'  => [
                ['Person', 'Wiki', 'Calendar', 'Report', 'Checkin'],
                $expected,
            ],
            'withEmptyString'  => [
                ['Person', 'Wiki', '', 'Calendar', 'Report', 'Checkin'],
                ['Wiki', 'Person', 'Report', 'Calendar', 'Checkin', ''],
            ]
        ];
    }


    public function testReturnDefaultEnStringIfNoConfigFileExists() : void
    {
        File::delete('./.changelogger.json');
        $config = new ChangeloggerConfig('.');

        $this->assertEquals('en', $config->getLanguage());
    }


    public function testReturnCustomLanguageSettingFromConfigFile() : void
    {
        File::put('./.changelogger.json', json_encode(['language' => 'de']));
        $config = new ChangeloggerConfig('.');

        $this->assertEquals('de', $config->getLanguage());
    }


    /**
     * @param array $unsorted
     * @param array $expected
     *
     * @dataProvider groupProvider
     */
    public function testComparingGroupsForSorting(array $unsorted, array $expected) : void
    {
        File::put('./.changelogger.json', json_encode(['groups' => $expected]));
        $config = new ChangeloggerConfig('.');

        uasort($unsorted, [$config, 'compare']);

        $this->assertEquals($expected, array_values($unsorted));
    }


    protected function tearDown() : void
    {
        File::delete('./.changelogger.json');
        parent::tearDown();
    }
}

<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ChangeloggerConfig
{
    /** @var Collection */
    private $config;

    public function __construct(string $root)
    {
        $this->config = collect();
        if (File::exists($root . '/.changelogger.json')) {
            $this->config = collect(json_decode(File::get($root . '/.changelogger.json'), true));
        }
    }


    /**
     * Return preferred language from configuration.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        if ($this->config->has('language')) {
            return $this->config->get('language');
        }

        return 'en';
    }
}

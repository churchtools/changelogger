<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ChangeloggerConfig
{

    /** @var Collection */
    private $config;


    public function __construct(string $root)
    {
        $this->config = new Collection();
        if (File::exists($root . '/.changelogger.json')) {
            $this->config = new Collection(json_decode(File::get($root . '/.changelogger.json'), true));
        }
    }


    /**
     * Return preferred language from configuration.
     *
     * @return string
     */
    public function getLanguage() : string
    {
        if ($this->config->has('language')) {
            return $this->config->get('language');
        }

        return 'en';
    }


    public function hasGroups(): bool
    {
        return $this->config->has('groups');
    }

    public function getGroups() : array
    {
        if ($this->config->has('groups') && is_array($this->config->get('groups'))) {
            return $this->config->get('groups');
        }

        return [];
    }


    public function getOrderFirst():string
    {
        if ($this->config->has('order-first')
            && in_array($this->config->get('order-first'), ['groups', 'types'])
        ) {
            return $this->config->get('order-first');
        }

        return 'types';
    }


    public function validateGroup(string $group): void
    {
        if ($group === '') {
            return;
        }

        if (! $this->hasGroups()) {
            throw new RuntimeException('No groups in config file. Please declare groups first.');
        }

        $groups = new Collection($this->getGroups());
        if ( ! $groups->contains($group)) {
            $options = $groups->implode(', ');
            throw new RuntimeException("No valid group. Use one of the following: {$options}");
        }

    }


    /**
     * Compare two groups. Sorting function
     *
     * @param string $groupA
     * @param string $groupB
     *
     * @return int
     */
    public function compare(string $groupA, string $groupB): int
    {
        $keyA = array_search($groupA, $this->getGroups(), true);
        $keyB = array_search($groupB, $this->getGroups(), true);

        return $keyA <=> $keyB;
    }
}

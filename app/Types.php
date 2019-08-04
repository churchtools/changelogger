<?php

namespace App;

class Types
{

    private $types = [
        'New feature'             => 'added',
        'Bug fix'                 => 'fixed',
        'Feature change'          => 'changed',
        'New deprecation'         => 'deprecated',
        'Feature removal'         => 'removed',
        'Security fix'            => 'security',
        'Performance improvement' => 'performance',
        'Other'                   => 'other',
        'No Changelog'            => 'ignore',
    ];


    /**
     * Find name of a given type.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getValue(string $key) : ?string
    {
        return collect($this->types)->get($key);
    }


    public function getName(string $key) : ?string
    {
        return collect($this->types)->filter(function ($type) use ($key) {
            return $type === $key;
        })->keys()->first();
    }

    /**
     * Validate type if it is a valid type.
     *
     * @param string|null $type
     *
     * @throws \RuntimeException
     */
    public function validate(?string $type) : void
    {
        if ( ! in_array($type, array_values($this->types), true)) {
            $options = implode(", ", array_values($this->types));
            throw new \RuntimeException("No valid type. Use one of the following: {$options}");
        }
    }


    /**
     * Get all types as array.
     *
     * @return array
     */
    public function getAll() : array
    {
        return $this->types;
    }


    /**
     * Get only keys of types.
     *
     * @return array
     */
    public function keys() : array
    {
        return array_keys($this->types);
    }
}

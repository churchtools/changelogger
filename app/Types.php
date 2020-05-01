<?php

namespace App;

use RuntimeException;

class Types
{

    private $types;

    public function __construct(ChangeloggerConfig $config)
    {
        $this->types = $config->getTypes();
    }

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
        return collect($this->types)->filter(static function ($type) use ($key) {
            return $type === $key;
        })->keys()->first();
    }


    /**
     * Validate type if it is a valid type.
     *
     * @param string|null $type
     *
     * @throws RuntimeException
     */
    public function validate(?string $type) : void
    {
        if ( ! in_array($type, array_values($this->types), true)) {
            $options = implode(", ", array_values($this->types));
            throw new RuntimeException("No valid type. Use one of the following: {$options}");
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

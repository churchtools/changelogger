<?php

namespace App;

class Types
{

    private static $types = [
        'New feature'             => 'added',
        'Bug fix'                 => 'fixed',
        'Feature change'          => 'changed',
        'New deprecation'         => 'deprecated',
        'Feature removal'         => 'removed',
        'Security fix'            => 'security',
        'Performance improvement' => 'performance',
        'Other'                   => 'other',
    ];

    public static function getName(string $key) : string
    {
        return collect(self::$types)->filter(function ($type) use ($key) {
            return $type === $key;
        })->keys()->first();
    }
}

<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

class LogEntry
{

    public const EMPTY = 'No changelog necessary';

    /** @var string */
    private $title;

    /** @var string */
    private $author;

    /** @var string */
    private $type;


    /**
     * LogEntry constructor.
     *
     * @param string $title
     * @param string $type
     * @param string $author
     */
    public function __construct(string $title, string $type, string $author)
    {
        $this->title  = $title;
        $this->author = $author;
        $this->type   = $type;
    }


    /**
     * Get log entry in YAML format.
     *
     * @return string
     */
    public function toYaml() : string
    {
        return Yaml::dump($this->toArray());
    }


    /**
     * Get log entry as array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'title'  => $this->title,
            'type'   => $this->type,
            'author' => $this->author,
        ];
    }

}

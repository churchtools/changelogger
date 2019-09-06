<?php

namespace App;

use Symfony\Component\Finder\SplFileInfo;
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

    /** @var string */
    private $group;


    /**
     * LogEntry constructor.
     *
     * @param string $title
     * @param string $type
     * @param string $author
     * @param string $group
     */
    public function __construct(string $title, string $type, string $author, ?string $group = null)
    {
        $this->title  = $title;
        $this->type   = $type;
        $this->author = $author;
        $this->group  = $group ?? '';
    }


    /**
     * Create new LogEntry object from a YAML file.
     *
     * @param $file
     *
     * @return LogEntry
     */
    public static function parse(SplFileInfo $file) : self
    {
        $content = Yaml::parse($file->getContents());

        if (! isset($content['group'])) {
            $content['group'] = '';
        }

        return new self($content['title'], $content['type'], $content['author'], $content['group']);
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
            'group'  => $this->group,
        ];
    }


    /**
     * Get Title.
     *
     * @return string
     */
    public function title() : string
    {
        return $this->title;
    }


    /**
     * Get Type.
     *
     * @return string
     */
    public function type() : string
    {
        return $this->type;
    }


    /**
     * Get Author.
     *
     * @return string
     */
    public function author() : string
    {
        return $this->author;
    }


    public function hasAuthor() : bool
    {
        return $this->author !== '';
    }


    public function group() : string
    {
        return $this->group;
    }

}

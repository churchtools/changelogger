<?php

namespace App;

use Illuminate\Support\Facades\File;

class ChangesDirectory
{

    /**
     * Path to changelog directory
     *
     * @var string
     */
    protected $path;


    /**
     * ChangesDirectory constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }


    /**
     * Initialize changelogs/unreleased directory.
     */
    public function init() : void
    {
        if ( ! File::exists($this->path)) {
            File::makeDirectory($this->path, 0755, true);
        }
    }


    /**
     * Check if any unreleased changes exists.
     *
     * @return bool
     */
    public function hasChanges() : bool
    {
        return count($this->getAll()) > 0;
    }


    /**
     * Get all files of unreleased changes.
     *
     * @return array<\SplFileInfo>
     */
    public function getAll() : array
    {
        return File::allFiles($this->getPath());
    }


    /**
     * Get path to unreleased changes directory.
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }


    /**
     * Clean $path.
     *
     * Remove all unreleased changes.
     */
    public function clean() : void
    {
        File::delete($this->getAll());
    }


    /**
     * Save log entry to unreleased changes on disk.
     *
     * @param LogEntry $logEntry
     * @param string   $filename
     *
     * @return bool
     */
    public function add(LogEntry $logEntry, string $filename) : bool
    {
        return File::put($this->getPath() . "/$filename", $logEntry->toYaml());
    }
}

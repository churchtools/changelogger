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
     */
    public function __construct()
    {
        $this->path = getcwd() . '/changelogs/unreleased';
        $this->initDirectory();
    }


    /**
     * Initialize changelogs/unreleased directory.
     */
    private function initDirectory() : void
    {
        if ( ! File::exists($this->path)) {
            File::makeDirectory($this->path, 0755, true);
        }
    }


    /**
     * Get path to unreleased changes directory.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * Get all files of unreleased changes.
     *
     * @return \SplFileInfo[]
     */
    public function getAll() : array
    {
        return File::allFiles($this->getPath());
    }


    /**
     * Check if any unreleased changes exists.
     *
     * @return bool
     */
    public function hasChanges() : bool
    {
        return ! empty($this->getAll());
    }
}

<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class Clean extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clean';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove all unreleased logs';

    /**
     * Path to changelog directory
     *
     * @var string
     */
    protected $path;


    /**
     * Clean constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->path = getcwd() . '/changelogs/unreleased';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->initFolder();
        $allFiles = File::allFiles($this->path);

        if (empty($allFiles)) {
            $this->info("No logs. Nothing to delete.");
            return;
        }

        $files = count($allFiles) === 1 ? '1 file' : count($allFiles) . ' files';
        $shouldDelete = $this->confirm("Do you want to delete {$files}?");

        if ($shouldDelete) {
            File::delete($allFiles);
            $this->task("Delete {$files}", function () {
                return true;
            });
        }
    }

    private function initFolder() : void
    {
        if ( ! File::exists($this->path)) {
            File::makeDirectory($this->path, 0755, true);
        }
    }
}

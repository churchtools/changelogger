<?php

namespace App\Commands;

use App\ChangesDirectory;
use LaravelZero\Framework\Commands\Command;

class CleanCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clean {--f|force : Force removing files}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove all unreleased logs';

    /** @var ChangesDirectory */
    protected $dir;


    /**
     * Clean constructor.
     *
     * @param ChangesDirectory $dir
     */
    public function __construct(ChangesDirectory $dir)
    {
        parent::__construct();
        $this->dir = $dir;
        $this->dir->init();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->dir->hasChanges()) {
            $this->info("No logs. Nothing to delete.");

            return;
        }

        $allFiles     = $this->dir->getAll();
        $files        = count($allFiles) === 1 ? '1 file' : count($allFiles) . ' files';

        $shouldDelete = $this->option('force') ? true : $this->confirm("Do you want to delete {$files}?");

        if ($shouldDelete) {
            $this->dir->clean();
            $this->task("Delete {$files}", function () {
                return true;
            });
        }
    }
}

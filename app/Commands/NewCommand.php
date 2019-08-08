<?php

namespace App\Commands;

use App\ChangesDirectory;
use App\LogEntry;
use App\Types;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;

class NewCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new 
                            {--f|force : Override existing changelog if one exists with the same name}
                            {--dry-run : Don\'t actually write anything, just print.}
                            {--t|type= : Type of changelog}
                            {--u|user : Use git user.name as author}
                            {--m|message= : Changelog entry}
                            {--i|file= : Filename, default is branch name}
                            {--empty : Add empty log}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new changelog';

    /** @var ChangesDirectory */
    private $dir;

    /** @var Types */
    private $types;


    /**
     * NewChangelog constructor.
     *
     * @param ChangesDirectory $dir
     * @param Types            $types
     */
    public function __construct(ChangesDirectory $dir, Types $types)
    {
        parent::__construct();
        $this->dir = $dir;
        $this->dir->init();
        $this->types = $types;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $title = $this->option('message');
        $type  = $this->option('type');
        $filename = $this->getFilename();
        $author   = $this->getAuthor();
        $empty    = $this->option('empty');

        if ($empty) {
            $title  = LogEntry::EMPTY;
            $type   = 'ignore';
            $author = '';
        }

        if ($type === null) {
            $type = $this->choice('Type of change', $this->types->keys());
            $type = $this->types->getValue($type);
        }

        try {
            $this->types->validate($type);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return;
        }

        while (empty($title)) {
            $title = $this->ask('Your changelog');
        }

        $logEntry = new LogEntry($title, $type, $author);

        if ( ! $this->option('dry-run')) {
            $this->dir->add($logEntry, $filename);
            $this->task("Saving Changelog changelogs/unreleased/$filename",
                function () {
                    return true;
                });
        }

        $this->info('Changelog generated:');
        $this->line($logEntry->toYaml());
    }


    /**
     * Get filename.
     *
     * @return string
     */
    private function getFilename() : string
    {
        $filename = $this->option('file');


        if (! $filename) {
            exec('git branch --show-current', $branch, $returnVar);

            if ($returnVar !== 0) {
                $filename = $this->ask("Filename");
            } else {
                $filename = preg_replace('/\//', '-', $branch[0]);
            }
        }

        $filename .= '.yml';

        if ( ! $this->option('force') && File::exists($this->dir->getPath() . "/$filename")) {
            $this->error('Changelog already exists. If you want to override the changelog use --force');
            exit(1);
        }

        return $filename;
    }


    private function getAuthor() : string
    {
        $author = '';

        if ($this->option('user')) {
            exec('git config user.name', $user, $returnVar);
            $author = $user[0];
        }

        return $author;
    }
}

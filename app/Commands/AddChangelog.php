<?php

namespace App\Commands;

use App\ChangesDirectory;
use App\LogEntry;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class AddChangelog extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'add 
                            {--f|force : Override existing changelog if one exists with the same name}
                            {--dry-run : Don\'t actually write anything, just print.}
                            {--t|type= : Type of changelog}
                            {--u|user : Use git user.name as author}
                            {--m|message= : Changelog entry}
                            {--empty : Add empty log}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new changelog';

    private $types = [
        'New feature'             => 'added',
        'Bug fix'                 => 'fixed',
        'Feature change'          => 'changed',
        'New deprecation'         => 'deprecated',
        'Feature removal'         => 'removed',
        'Security fix'            => 'security',
        'Performance improvement' => 'performance',
        'Other'                   => 'other',
    ];

    /** @var ChangesDirectory */
    private $dir;


    /**
     * AddChangelog constructor.
     *
     * @param ChangesDirectory $dir
     */
    public function __construct(ChangesDirectory $dir)
    {
        parent::__construct();
        $this->dir = $dir;
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
        $this->validateType($type);
        $filename = $this->getFilename();
        $author   = $this->getAuthor();
        $empty    = $this->option('empty');

        if ($empty) {
            $title  = LogEntry::EMPTY;
            $type   = 'ignore';
            $author = '';
        }

        if ($type === null) {
            $type = $this->choice('Type of change', array_keys($this->types));
            $type = $this->types[$type];
        }

        while (empty($title)) {
            $title = $this->ask('Your changelog');
        }

        $logEntry = new LogEntry($title, $type, $author);

        if ( ! $this->option('dry-run')) {
            $this->task("Saving Changelog changelogs/unreleased/$filename",
                function () use ($logEntry, $filename) {
                    return $this->dir->add($logEntry, $filename);
                });
        }

        $this->info('Changelog generated:');
        $this->line($logEntry->toYaml());
    }


    private function validateType(?string $type) : void
    {
        if ($type === null) {
            return;
        }

        if ( ! in_array($type, array_values($this->types), true)) {
            $this->error('No valid type. Use one of the following:');
            $this->line(implode(", ", array_values($this->types)));
            die();
        }
    }


    /**
     * Get filename.
     *
     * @return string
     */
    private function getFilename() : string
    {
        exec('git branch --show-current', $branch, $returnVar);

        if ($returnVar !== 0) {
            $filename = $this->ask("Filename");
        } else {
            $filename = preg_replace('/\//', '-', $branch[0]);
        }

        $filename .= '.yml';

        if (File::exists($this->dir->getPath() . "/$filename") && ! $this->option('force')) {
            $this->error('Changelog already exists. If you want to override the changelog use --force');
            die();
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

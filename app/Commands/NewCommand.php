<?php

namespace App\Commands;

use App\ChangeloggerConfig;
use App\ChangesDirectory;
use App\LogEntry;
use App\Types;
use Carbon\Carbon;
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
                            {--g|group= : Name of group, specified in config}
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

    /** @var ChangeloggerConfig */
    private $config;


    /**
     * NewChangelog constructor.
     *
     * @param ChangesDirectory   $dir
     * @param Types              $types
     * @param ChangeloggerConfig $config
     */
    public function __construct(ChangesDirectory $dir, Types $types, ChangeloggerConfig $config)
    {
        parent::__construct();
        $this->dir = $dir;
        $this->dir->init();
        $this->types  = $types;
        $this->config = $config;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $title  = (string) $this->option('message');
        $type   = $this->option('type');
        $author = $this->getAuthor();
        $empty  = $this->option('empty');
        $group  = $this->option('group');

        if ($empty) {
            $title  = LogEntry::EMPTY;
            $type   = 'ignore';
            $author = '';
            $group  = '';
        }

        if ($type === null) {
            $type = $this->choice('Type of change', $this->types->keys());
            $type = $this->types->getValue($type);

            if ($type === 'ignore') {
                $title  = LogEntry::EMPTY;
                $type   = 'ignore';
                $author = '';
                $group  = '';
            }
        }

        if ($group === null && $this->config->hasGroups()) {
            $group = $this->choice('Group of change', $this->config->getGroups());
        }

        try {
            $this->types->validate($type);
            if ($this->config->hasGroups()) {
                $this->config->validateGroup($group);
            }
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return;
        }

        while ($title === '') {
            $title = $this->ask('Your changelog');
        }

        $logEntry = new LogEntry($title, $type, $author, $group);

        if ( ! $this->option('dry-run')) {
            $filename = $this->getFilename($type);
            $this->dir->add($logEntry, $filename);
            $this->task("Saving Changelog changelogs/unreleased/$filename", static function () {
                return true;
            });
        }

        $this->info('Changelog generated:');
        $this->line($logEntry->toYaml());
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


    /**
     * Get filename.
     *
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
    private function getFilename(string $type) : string
    {
        $filename = $this->option('file');

        if ( ! $filename) {
            $now      = (new Carbon())->format("Y-m-d-his");
            $filename = $type;

            /**
             * Command to get current branch name.
             * @see https://stackoverflow.com/questions/6245570/how-to-get-the-current-branch-name-in-git
             */
            exec('which git', $path, $returnVarWhich);
            if ($returnVarWhich === 0 && count($path) > 0) {
                exec("git branch | grep \\* | cut -d ' ' -f2", $branch, $returnVarBranch);
                if ($returnVarBranch === 0 && isset($branch[0])) {
                    $filename = preg_replace('/\//', '-', $branch[0]);
                }
            }
            $filename = "{$now}-{$filename}";
        }

        $filename .= '.yml';

        if ( ! $this->option('force') && File::exists($this->dir->getPath() . "/$filename")) {
            $this->error('Changelog already exists. If you want to override the changelog use --force');
            exit(1);
        }

        return $filename;
    }
}

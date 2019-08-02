<?php

namespace App\Commands;

use App\ChangesDirectory;
use App\LogEntry;
use App\Types;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class BuildChangelog extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build {tag : Version or tag name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build new changelog from unreleased logs';

    /** @var ChangesDirectory */
    private $dir;

    /** @var Types */
    private $types;


    /**
     * BuildChangelog constructor.
     *
     * @param ChangesDirectory $dir
     * @param Types            $types
     */
    public function __construct(ChangesDirectory $dir, Types $types)
    {
        parent::__construct();
        $this->dir   = $dir;
        $this->types = $types;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ( ! $this->dir->hasChanges()) {
            $this->build('No changes.');
            exit();
        }

        $changes = collect();
        foreach ($this->dir->getAll() as $file) {
            $changes->push(LogEntry::parse($file));
        }

        $content = $this->generateContent($changes);
        $this->build($content);

        $this->info("Changelog for {$this->argument('tag')} created");
        $this->task('Clean unreleased changes', function () {
            $this->dir->clean();
        });
    }


    private function build(string $string) : void
    {
        $tag   = $this->argument('tag');
        $today = Carbon::now()->format('Y-m-d');

        if (File::exists(getcwd() . '/CHANGELOG.md')) {
            $fileContent = File::get(getcwd() . '/CHANGELOG.md');
        }

        $content = <<<CONTENT
<!-- CHANGELOGER -->

## [$tag] - $today

$string
CONTENT;

        if (isset($fileContent)) {
            $content = preg_replace('/<!-- CHANGELOGER -->/', $content, $fileContent);
        }

        File::put(getcwd() . '/CHANGELOG.md', $content);
    }


    private function generateContent(Collection $changes) : string
    {
        $changes = $changes->groupBy(function (LogEntry $logEntry) {
            return $logEntry->type();
        })->filter(function (Collection $logType, $key) {
            return $key !== 'ignore';
        })->sort();

        return $changes->map(function (Collection $logType, $key) {
            $header  = $this->types->getName($key);
            $count   = $logType->count();
            $changes = sprintf('%d %s', $count, $count === 1 ? 'change' : 'changes');
            $content = "### {$header} ({$changes})\n\n";

            $content .= $logType->map(function (LogEntry $log) {
                $changeEntry = "* {$log->title()}";

                if ( ! empty($log->author())) {
                    $changeEntry .= " (props {$log->author()})";
                }

                return $changeEntry;
            })->implode("\n");

            $content .= "\n";

            return $content;
        })->implode("\n");
    }
}

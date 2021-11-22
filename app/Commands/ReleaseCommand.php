<?php

namespace App\Commands;

use App\ChangeloggerConfig;
use App\ChangesDirectory;
use App\LogEntry;
use App\Types;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class ReleaseCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'release {tag : Version or tag name}';

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

    /** @var ChangeloggerConfigConfig */
    private $config;


    /**
     * BuildChangelog constructor.
     *
     * @param ChangesDirectory   $dir
     * @param Types              $types
     * @param ChangeloggerConfig $config
     */
    public function __construct(ChangesDirectory $dir, Types $types, ChangeloggerConfig $config)
    {
        parent::__construct();
        $this->dir   = $dir;
        $this->dir->init();
        $this->types = $types;
        $this->config = $config;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ( ! $this->dir->hasChanges()) {
           $this->info("No Changes -> No Changelog for {$this->argument('tag')} created");
           return;
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

        if (File::exists(config('changelogger.directory') . '/CHANGELOG.md')) {
            $fileContent = File::get(config('changelogger.directory') . '/CHANGELOG.md');
        }

        $content = <<<CONTENT
<!-- CHANGELOGGER -->

## [$tag] - $today

$string
CONTENT;

        if (isset($fileContent)) {
            $content = preg_replace('/<!-- CHANGELOGGER -->/', $content, $fileContent);
        }

        File::put(config('changelogger.directory') . '/CHANGELOG.md', $content);
    }


    private function generateContent(Collection $changes) : string
    {
        $groupByFunctions[] = static function (LogEntry $logEntry) {
            return $logEntry->type();
        };

        if ($this->config->hasGroups()) {
            $groupByFunctions[] = static function (LogEntry $logEntry) {
                return $logEntry->group();
            };
        }

        $changes = $changes->groupBy($groupByFunctions)->filter(static function (Collection $logType, $key) {
            return $key !== 'ignore';
        })->sort();

        return $changes->map(function (Collection $logType, $key) {
            $header  = $this->types->getName($key);
            $count   = $logType->count();
            $changes = sprintf('%d %s', $count, $count === 1 ? 'change' : 'changes');
            $content = "### {$header} ({$changes})\n\n";
            $markdownOptions = $this->config->getMarkdownOptions();

            if ($this->config->hasGroups()) {
                $content .= $logType->sort(function (Collection $logA,  Collection $logB) {
                    return $this->config->compare($logA->first()->group(), $logB->first()->group());
                })->map(static function (Collection $group, $name) use ($markdownOptions){
                    if ($markdownOptions['groupsAsList']) {
                        $content = "{$markdownOptions['listStyle']} **{$name}**\n";
                    } else {
                        $content = "#### {$name}\n\n";
                    }

                    $content .= $group->map(static function (LogEntry $log) use ($markdownOptions) {
                        $changeEntry = "";
                        if ($markdownOptions['groupsAsList']) {
                            $changeEntry = "  ";
                        }
                        $changeEntry .= "{$markdownOptions['listStyle']} {$log->title()}";

                        if ($log->hasAuthor()) {
                            $changeEntry .= " (props {$log->author()})";
                        }

                        return $changeEntry;
                    })->implode("\n");

                    return $content;
                })->implode("\n\n");
            } else {
                $content .= $logType->map(static function (LogEntry $log) use ($markdownOptions) {
                    $changeEntry = "{$markdownOptions['listStyle']} {$log->title()}";

                    if ($log->hasAuthor()) {
                        $changeEntry .= " (props {$log->author()})";
                    }

                    return $changeEntry;
                })->implode("\n");

            }

            $content .= "\n";
            return $content;
        })->implode("\n");
    }
}

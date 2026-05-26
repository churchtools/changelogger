<?php

namespace App\Commands;

use App\ChangeloggerConfig;
use App\ChangesDirectory;
use App\LogEntry;
use App\Types;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class ShowCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'show {version? : Released version to show}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show unreleased or released changes';

    /** @var ChangesDirectory */
    private $dir;

    /** @var Types */
    private $types;

    /** @var ChangeloggerConfig */
    private $config;


    /**
     * ShowCommand constructor.
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
        if ($this->argument('version') !== null) {
            return $this->showReleasedVersion($this->argument('version'));
        }

        $this->line($this->generateUnreleasedMarkdown());
        return 0;
    }


    private function generateUnreleasedMarkdown() : string
    {
        $changes = collect();
        foreach ($this->dir->getAll() as $file) {
            $changes->push(LogEntry::parse($file));
        }

        return $this->generateContent($changes);
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


    private function showReleasedVersion(string $version) : int
    {
        $changelogPath = config('changelogger.directory') . '/CHANGELOG.md';

        if ( ! File::exists($changelogPath)) {
            return $this->releaseNotFound($version);
        }

        $releaseSection = $this->findReleaseSection(File::get($changelogPath), $version);

        if ($releaseSection === null) {
            return $this->releaseNotFound($version);
        }

        $this->line($releaseSection);
        return 0;
    }


    private function findReleaseSection(string $changelog, string $version) : ?string
    {
        $quotedVersion = preg_quote($version, '/');
        $pattern = "/^## \[{$quotedVersion}\].*?(?=\n## \[|\z)/ms";

        if (preg_match($pattern, $changelog, $matches) !== 1) {
            return null;
        }

        return rtrim($matches[0]);
    }


    private function releaseNotFound(string $version) : int
    {
        $this->error("Error: Release \"{$version}\" was not found in CHANGELOG.md.");
        return 1;
    }
}

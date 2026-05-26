<?php

namespace App\Commands;

use App\ChangelogMarkdownGenerator;
use App\ChangesDirectory;
use App\LogEntry;
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

    /** @var ChangelogMarkdownGenerator */
    private $markdownGenerator;


    /**
     * ShowCommand constructor.
     *
     * @param ChangesDirectory          $dir
     * @param ChangelogMarkdownGenerator $markdownGenerator
     */
    public function __construct(ChangesDirectory $dir, ChangelogMarkdownGenerator $markdownGenerator)
    {
        parent::__construct();
        $this->dir = $dir;
        $this->dir->init();
        $this->markdownGenerator = $markdownGenerator;
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

        return $this->markdownGenerator->generate($changes);
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

<?php

namespace App\Commands;

use App\ChangelogMarkdownGenerator;
use App\ChangesDirectory;
use App\LogEntry;
use Carbon\Carbon;
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

    /** @var ChangelogMarkdownGenerator */
    private $markdownGenerator;


    /**
     * BuildChangelog constructor.
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
        if ( ! $this->dir->hasChanges()) {
           $this->info("No Changes -> No Changelog for {$this->argument('tag')} created");
           $this->dir->clean();
           return;
        }

        $changes = collect();
        foreach ($this->dir->getAll() as $file) {
            $changes->push(LogEntry::parse($file));
        }

        $content = $this->markdownGenerator->generate($changes);
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


}

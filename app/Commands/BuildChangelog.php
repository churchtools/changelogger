<?php

namespace App\Commands;

use App\Types;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;

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

    /**
     * Path to changelog directory
     *
     * @var string
     */
    protected $path;


    /**
     * BuildChangelog constructor.
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
            $this->build('No changes.');
            exit();
        }

        $changes = collect();
        foreach ($allFiles as $file) {
            $changes->push(Yaml::parse($file->getContents()));
        }

        $content = $this->generateContent($changes);
        $this->build($content);

        $this->info("Changelog for {$this->argument('tag')} created");
        File::delete($allFiles);
    }


    private function initFolder() : void
    {
        if ( ! File::exists($this->path)) {
            File::makeDirectory($this->path, 0755, true);
        }
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
        $changes = $changes->groupBy('type')->filter(function (Collection $logType, $key) {
            return $key !== 'ignore';
        });

        return $changes->map(function (Collection $logType, $key) {
            $header  = Types::getName($key);
            $count   = $logType->count();
            $changes = sprintf('%d %s', $count, $count === 1 ? 'change' : 'changes');
            $content = "### {$header} ({$changes})\n\n";

            $content .= $logType->map(function (array $log) {
                $changeEntry = "* {$log['title']}";

                if ( ! empty($log['author'])) {
                    $changeEntry .= " (props {$log['author']})";
                }

                return $changeEntry;
            })->implode("\n");

            $content .= "\n";

            return $content;
        })->implode("\n");
    }
}

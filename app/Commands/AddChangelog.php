<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;

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
                            {--m|message= : Changelog entry}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new changelog';

    /**
     * Path to changelog directory
     *
     * @var string
     */
    protected $path;

    protected $types = [
        'New feature'             => 'added',
        'Bug fix'                 => 'fixed',
        'Feature change'          => 'changed',
        'New deprecation'         => 'deprecated',
        'Feature removal'         => 'removed',
        'Security fix'            => 'security',
        'Performance improvement' => 'performance',
        'Other'                   => 'other',
    ];


    /**
     * MakeChangelog constructor.
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
        $title = $this->option('message');
        $type  = $this->option('type');
        $this->validateType($type);
        $filename = $this->getFilename();
        $author   = $this->getAuthor();

        if ($type === null) {
            $type = $this->choice('Type of change', array_keys($this->types));
            $type = $this->types[$type];
        }

        while (empty($title)) {
            $title = $this->ask('Your changelog');
        }

        $content = [
            'title'  => $title,
            'type'   => $type,
            'author' => $author,
        ];

        if ( ! $this->option('dry-run')) {
            File::put($this->path . "/$filename", Yaml::dump($content));
            $this->task("Saving Changelog changelogs/unreleased/$filename", function () {
                return true;
            });
        }

        $this->info('Changelog generated:');
        $this->line(Yaml::dump($content));
    }


    private function initFolder() : void
    {
        if ( ! File::exists($this->path)) {
            File::makeDirectory($this->path, 0755, true);
        }
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

        if (File::exists($this->path . "/$filename") && ! $this->option('force')) {
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


    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule) : void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}

<?php

namespace App\Commands;

use App\ChangeloggerConfig;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\App;
use LaravelZero\Framework\Commands\Command;

class InfoCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'info';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Information about changelogger';

    private $config;


    /**
     * InfoCommand constructor.
     *
     * @param $config
     */
    public function __construct(ChangeloggerConfig $config)
    {
        parent::__construct();
        $this->config = $config;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $version = App::version();
        $this->alert("Changelogger - $version");
        $this->line("Changelogger is a simple CLI tool to help you creating new consistent changelog entries.\n");
        $this->info('For more information what a changelog is and why you need one, see: <comment>https://keepachangelog.com</comment>');
        $this->info("Language used: {$this->config->getLanguage()}");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}

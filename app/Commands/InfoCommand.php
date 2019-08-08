<?php

namespace App\Commands;

use App\ChangeloggerConfig;
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

    /**
     * @var ChangeloggerConfig
     */
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
    public function handle() : void
    {
        $version = App::version();
        $this->alert("Changelogger - $version");
        $this->line("Changelogger is a simple CLI tool to help you creating new consistent changelog entries.\n");
        $this->info('For more information what a changelog is and why you need one, see: <comment>https://keepachangelog.com</comment>');
        $this->info("Language used: {$this->config->getLanguage()}");
    }
}

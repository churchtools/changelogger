<?php

namespace App\Commands;

use App\ChangeloggerConfig;
use App\ChangesDirectory;
use App\LogEntry;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class ShowCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'show';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show unreleased changes';

    /** @var ChangesDirectory */
    private $dir;

    /** @var ChangeloggerConfig */
    private $config;


    /**
     * ShowCommand constructor.
     *
     * @param ChangesDirectory   $dir
     * @param ChangeloggerConfig $config
     */
    public function __construct(ChangesDirectory $dir, ChangeloggerConfig $config)
    {
        parent::__construct();
        $this->dir = $dir;
        $this->dir->init();
        $this->config = $config;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->table($this->tableHeaders(), $this->tableRows());
    }


    private function tableHeaders() : array
    {
        if ($this->config->hasGroups()) {
            return ['No.', 'Type', 'Group', 'Log', 'Author'];
        }

        return ['No.', 'Type', 'Log', 'Author'];
    }


    private function tableRows() : array
    {
        $hasGroups          = $this->config->hasGroups();
        $groupByFunctions[] = static function (LogEntry $logEntry) {
            return $logEntry->type();
        };

        if ($hasGroups) {
            $groupByFunctions[] = static function (LogEntry $logEntry) {
                return $logEntry->group();
            };
        }

        $changes = collect();
        foreach ($this->dir->getAll() as $file) {
            $changes->push(LogEntry::parse($file));
        }

        return $changes->groupBy($groupByFunctions)->filter(static function (
                Collection $logType,
                $key
            ) {
                return $key !== 'ignore';
            })->sort()->flatten()->map(static function (LogEntry $log, $key) use ($hasGroups) {
                if ($hasGroups) {
                    return [
                        $key + 1,
                        $log->type(),
                        $log->group(),
                        $log->title(),
                        $log->author()
                    ];
                }

                return [
                    $key + 1,
                    $log->type(),
                    $log->title(),
                    $log->author()
                ];
            })->toArray();
    }
}

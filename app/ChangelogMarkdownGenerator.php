<?php

namespace App;

use Illuminate\Support\Collection;

class ChangelogMarkdownGenerator
{

    /** @var Types */
    private $types;

    /** @var ChangeloggerConfig */
    private $config;


    public function __construct(Types $types, ChangeloggerConfig $config)
    {
        $this->types = $types;
        $this->config = $config;
    }


    public function generate(Collection $changes) : string
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

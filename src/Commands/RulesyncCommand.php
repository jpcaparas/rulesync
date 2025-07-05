<?php

namespace JPCaparas\Rulesync\Commands;

use Illuminate\Console\Command;

class RulesyncCommand extends Command
{
    public $signature = 'rulesync';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

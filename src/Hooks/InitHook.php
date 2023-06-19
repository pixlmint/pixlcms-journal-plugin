<?php

namespace PixlMint\JournalPlugin\Hooks;

use Nacho\Hooks\AbstractHook;
use PixlMint\CMS\Contracts\InitFunction;
use PixlMint\JournalPlugin\Helpers\JournalConfiguration;

class InitHook extends AbstractHook implements InitFunction
{
    public function call(array $init): array
    {
        $init['journalVersion'] = JournalConfiguration::version();
        $init['journalYear'] = JournalConfiguration::year();

        return $init;
    }
}
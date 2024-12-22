<?php

namespace PixlMint\JournalPlugin\Hooks;

use PixlMint\CMS\Contracts\InitFunction;
use PixlMint\JournalPlugin\Helpers\JournalConfiguration;

class InitHook implements InitFunction
{
    private JournalConfiguration $configuration;

    public function __construct(JournalConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function call(array $init): array
    {
        $init['feVersion'] = $this->configuration->version();
        $init['feYear'] = $this->configuration->year();

        return $init;
    }
}

<?php

namespace PixlMint\JournalPlugin\Helpers;

use Nacho\Exceptions\ConfigurationDoesNotExistException;
use Nacho\Helpers\ConfigurationContainer;

class JournalConfiguration
{
    private ConfigurationContainer $configurationContainer;

    public function __construct(ConfigurationContainer $configurationContainer)
    {
        $this->configurationContainer = $configurationContainer;
    }

    public function year(): mixed
    {
        return $this->getJournalConfig('year');
    }

    public function version(): string
    {
        return $this->getJournalConfig('version');
    }

    private function getJournalConfig(string $configName): mixed
    {
        $config = $this->configurationContainer->getCustomConfig('journal');

        if (!key_exists($configName, $config)) {
            throw new ConfigurationDoesNotExistException("{$configName} does not exist in journal configuration");
        }

        return $config[$configName];
    }

}
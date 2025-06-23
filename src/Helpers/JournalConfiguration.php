<?php

namespace PixlMint\JournalPlugin\Helpers;

use PixlMint\CMS\Helpers\PluginConfiguration;

class JournalConfiguration extends PluginConfiguration
{
    public function year(): mixed
    {
        return $this->getPluginConfigValue('year');
    }

    public function version(): string
    {
        return $this->getPluginConfigValue('version');
    }

    protected function getPluginConfigKey(): string
    {
        return 'journal';
    }
}

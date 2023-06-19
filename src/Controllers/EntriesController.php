<?php

namespace PixlMint\JournalPlugin\Controllers;

use Nacho\Controllers\AbstractController;
use PixlMint\JournalPlugin\Helpers\CacheHelper;
use PixlMint\JournalPlugin\Helpers\JournalConfiguration;

class EntriesController extends AbstractController
{
    public function loadEntries(): string
    {
        $cacheHelper = new CacheHelper($this->nacho);
        $cache = $cacheHelper->read();
        if (!$cache) {
            $cacheHelper->build();
            $cache = $cacheHelper->read();
        }
        $content = $cache->getContent();

        if (!$this->journalIsCurrentYear()) {
            $content = $this->reverseEntries($content);
        }

        return $this->json($content);
    }

    private function reverseEntries(array $content): array
    {
        return array_reverse(array_map(function(array $month) {
            $month['days'] = array_reverse($month['days']);
            return $month;
        }, $content));
    }

    private function journalIsCurrentYear(): bool
    {
        $journalYear = JournalConfiguration::year();
        $now = new \DateTime();
        $currentYear = intval($now->format('Y'));

        return $journalYear === $currentYear;
    }
}
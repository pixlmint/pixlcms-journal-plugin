<?php

namespace PixlMint\JournalPlugin\Controllers;

use Nacho\Controllers\AbstractController;
use PixlMint\CMS\Models\Cache;
use PixlMint\JournalPlugin\Helpers\CacheHelper;
use PixlMint\JournalPlugin\Helpers\JournalConfiguration;

class EntriesController extends AbstractController
{
    public function loadEntries(): string
    {
        $cache = $this->getCachedMonths();

        $content = $cache->getContent();

        if (!$this->journalIsCurrentYear()) {
            $content = $this->reverseEntries($content);
        }

        return $this->json($content);
    }

    public function getMonthsList(): string
    {
        return $this->json($this->listMonths());
    }

    public function loadMonth(): string
    {
        $cachedEntries = $this->getCachedMonths();
        if (key_exists('month', $_GET)) {
            $month = $_GET['month'];
        } elseif (key_exists('monthIndex', $_GET)) {
            $monthIndex = $_GET['monthIndex'];
            $months = $this->listMonths();
            $month = $months[$monthIndex];
        } else {
            return $this->json(['Please define month or monthIndex'], 400);
        }
        $monthEntries = $cachedEntries->getContent()[$month];

        if (!$this->journalIsCurrentYear()) {
            $monthEntries['days'] = array_reverse($monthEntries['days']);
        }

        return $this->json($monthEntries);
    }

    private function listMonths(): array
    {
        $cachedEntries = $this->getCachedMonths();

        $months = [];
        foreach ($cachedEntries->getContent() as $month => $content) {
            $months[] = $month;
        }

        if (!$this->journalIsCurrentYear()) {
            $months = array_reverse($months);
        }

        return $months;
    }

    private function getCachedMonths(): Cache
    {
        $cacheHelper = new CacheHelper($this->nacho);
        $cache = $cacheHelper->read();
        if (!$cache) {
            $cacheHelper->build();
            $cache = $cacheHelper->read();
        }

        return $cache;
    }

    private function reverseEntries(array $content): array
    {
        return array_reverse(array_map(function (array $month) {
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
<?php

namespace PixlMint\JournalPlugin\Helpers;

use Nacho\Contracts\PageManagerInterface;
use Nacho\Models\PicoPage;
use Nacho\ORM\RepositoryInterface;
use PixlMint\CMS\Models\Cache;
use PixlMint\CMS\Repository\CacheRepository;

class CacheHelper
{
    private CacheRepository|RepositoryInterface $repository;
    private PageManagerInterface $pageManager;
    const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    public function __construct(PageManagerInterface $pageManager, CacheRepository $repository) {
        $this->pageManager = $pageManager;
        $this->repository = $repository;
    }

    public function build(): void
    {
        $content = $this->renderContent();
        $renderDate = date('Y-m-d H:i:s', time());
        $cache = new Cache($renderDate, $content);

        $this->repository->set($cache);
    }

    public function read(): ?Cache
    {
        return $this->repository->getById(1);
    }

    private function renderContent(): array
    {
        $this->pageManager->readPages();
        $pages = $this->pageManager->getPages();
        $pages = $this->filterMetaEntries($pages);
        usort($pages, [$this, 'sortByDate']);
        $months = [];
        foreach ($pages as $page) {
            /** @var PicoPage $page */
            $month = explode('/', $page->id)[1];
            if (!key_exists($month, $months)) {
                $months[$month] = [
                    'name' => $month,
                    'days' => [],
                ];
            }
            if ($this->isEmptyContent($page)) {
                continue;
            }
            $page->content = $this->pageManager->renderPage($page);
            $months[$month]['days'][] = $page->toArray();
        }
        // print_r($months);

        return $months;
    }

    /**
     * Removes anything that isn't an actual Journal Entry
     *
     * @param array|PicoPage[] $entries
     * @return array|PicoPage[]
     */
    private function filterMetaEntries(array $entries): array
    {
        $ret = [];

        foreach ($entries as $url => $entry) {
            /** @var PicoPage $entry */
            if (!str_ends_with($entry->file, "index.md")) {
                $ret[$url] = $entry;   
            }
        }

        return $ret;
    }

    private function sortByDate(PicoPage $a, PicoPage $b): int
    {
        $t1 = date_create_from_format("d.m.Y", $a->meta->title)->getTimestamp();
        $t2 = date_create_from_format("d.m.Y", $b->meta->title)->getTimestamp();

        return $t2 - $t1;
    }

    private function isEmptyContent(PicoPage $page): bool
    {
        return !$page->raw_content && !key_exists('raceReport', $page->meta->toArray());
    }

}

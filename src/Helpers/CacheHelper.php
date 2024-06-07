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
        usort($pages, [$this, 'sortByDate']);
        $months = [];
        foreach ($pages as $page) {
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
            $months[$month]['days'][] = $page;
        }

        return $months;
    }

    private function sortByDate(PicoPage $a, PicoPage $b): int
    {
        if (is_int(array_search($a->meta->title, self::MONTHS))) {
            return -1;
        }
        if (is_int(array_search($b->meta->title, self::MONTHS))) {
            return 1;
        }
        $t1 = strtotime($a->meta->title);
        $t2 = strtotime($b->meta->title);

        return $t2 - $t1;
    }

    private function isEmptyContent(PicoPage $page): bool
    {
        return !$page->raw_content && !key_exists('raceReport', $page->meta->toArray());
    }

}
<?php

namespace PixlMint\JournalPlugin\Controllers;

use DateTime;
use Nacho\Contracts\PageManagerInterface;
use Nacho\Contracts\RequestInterface;
use Nacho\Controllers\AbstractController;
use Nacho\Helpers\PicoVersioningHelper;
use Nacho\Models\HttpResponse;
use Nacho\Models\Request;
use PixlMint\CMS\Helpers\CustomUserHelper;
use PixlMint\JournalPlugin\Helpers\CacheHelper;
use PixlMint\JournalPlugin\Models\RaceReport;
use PixlMint\CMS\Controllers\AdminController as CmsAdminController;

class AdminController extends AbstractController
{
    private PageManagerInterface $pageManager;

    public function __construct(PageManagerInterface $pageManager)
    {
        parent::__construct();
        $this->pageManager = $pageManager;
    }

    /**
     * Extends the primary Edit Controller to allow automatic recaching
     */
    public function edit(RequestInterface $request, CmsAdminController $parent, CacheHelper $cacheHelper, PicoVersioningHelper $versioningHelper): HttpResponse
    {
        $ret = $parent->edit($request, $versioningHelper);
        $cacheHelper->build();

        return $ret;
    }

    public function editCurrent(): HttpResponse
    {
        $file = $this->getCurrentFile();

        return $this->json(['entryId' => rtrim($file, '.md')]);
    }

    public function uploadRaceReport(Request $request): HttpResponse
    {
        if (!$request->getBody()->has('token') || !$request->getBody()->has('raceReport') || !$request->getBody()->has('entry')) {
            return $this->json(['message' => 'You need to provide a token, raceReport and the entry'], 400);
        }
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You need to be authenticated'], 401);
        }
        $raceReport = new RaceReport($request->getBody()['raceReport']);
        $entry = $this->pageManager->getPage($request->getBody()->get('entry'));
        if (!$entry) {
            $this->createSpecific();
        }
        $this->pageManager->editPage($entry->id, '', ['raceReport' => (array)$raceReport]);

        return $this->json(['message' => 'Successfully stored Race Report']);
    }

    public function delete(RequestInterface $request, CmsAdminController $cmsAdminController, CacheHelper $cacheHelper): HttpResponse
    {
        $response = $cmsAdminController->delete($request);
        $cacheHelper->build();

        return $response;
    }

    public function createSpecific(): HttpResponse
    {
        $dateFile = $_REQUEST['entry'];
        $entryId = $this->createFileIfNotExists(new DateTime($dateFile));
        return $this->json(['entryId' => $entryId]);
    }

    private function getCurrentFile(): string
    {
        $now = new \DateTime();
        return $this->createFileIfNotExists($now);
    }

    private function createFileIfNotExists(DateTime $dateEntry): string
    {
        $title = $dateEntry->format('Y-m-d');
        $month = $dateEntry->format('F');
        $parentId = strtolower("/$month");
        $entryId = strtolower("/$month/$title");

        if (is_null($this->pageManager->getPage($parentId))) {
            $this->pageManager->create("/", $month, true);
        }

        if (is_null($this->pageManager->getPage($entryId))) {
            $this->pageManager->create($parentId, $title);
        }

        return $entryId;
    }
}


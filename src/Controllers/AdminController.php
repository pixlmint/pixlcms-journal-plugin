<?php

namespace PixlMint\JournalPlugin\Controllers;

use DateTime;
use Nacho\Contracts\PageManagerInterface;
use Nacho\Contracts\RequestInterface;
use Nacho\Controllers\AbstractController;
use Nacho\Helpers\PicoVersioningHelper;
use Nacho\Models\HttpMethod;
use Nacho\Models\HttpResponse;
use Nacho\Models\Request;
use Nacho\Nacho;
use PixlMint\CMS\Helpers\CMSConfiguration;
use PixlMint\CMS\Helpers\CustomUserHelper;
use PixlMint\JournalPlugin\Helpers\CacheHelper;
use PixlMint\JournalPlugin\Models\RaceReport;

class AdminController extends AbstractController
{
    private PageManagerInterface $pageManager;
    private CMSConfiguration $cmsConfiguration;

    public function __construct(PageManagerInterface $pageManager, CMSConfiguration $cmsConfiguration)
    {
        parent::__construct();
        $this->pageManager = $pageManager;
        $this->cmsConfiguration = $cmsConfiguration;
    }

    public function edit(RequestInterface $request, \PixlMint\CMS\Controllers\AdminController $parent, CacheHelper $cacheHelper, PicoVersioningHelper $versioningHelper): HttpResponse
    {
        $ret = $parent->edit($request, $versioningHelper);

        if (strtoupper($request->requestMethod) === HttpMethod::PUT) {
            $cacheHelper->build();
        }

        return $ret;
    }

    public function editCurrent(): HttpResponse
    {
        $file = $this->getCurrentFile();

        return $this->json(['entryId' => rtrim($file, '.md')]);
    }

    public function uploadRaceReport(Request $request): HttpResponse
    {
        if (!key_exists('token', $request->getBody()) || !key_exists('raceReport', $request->getBody()) || !key_exists('entry', $request->getBody())) {
            return $this->json(['message' => 'You need to provide a token, raceReport and the entry'], 400);
        }
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You need to be authenticated'], 401);
        }
        $raceReport = new RaceReport($request->getBody()['raceReport']);
        $entry = $this->pageManager->getPage($request->getBody()['entry']);
        if (!$entry) {
            $this->createSpecific();
        }
        $this->pageManager->editPage($entry->id, '', ['raceReport' => (array)$raceReport]);

        return $this->json(['message' => 'Successfully stored Race Report']);
    }

    function createSpecific(): HttpResponse
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
        $title = $dateEntry->format('Y-m-d') . '.md';
        $month = $dateEntry->format('F');
        $folderDir = $this->cmsConfiguration->contentDir() . DIRECTORY_SEPARATOR . $month;
        $fileDir = "${folderDir}/${title}";
        $content =
            "---\ntitle: " .
            rtrim($title, '.md') .
            "\ndate: " .
            $dateEntry->format('Y-m-d H:i') .
            "\n---\n";

        // check if file exists, if not create it
        if (!is_dir($folderDir)) {
            mkdir($folderDir);
        }
        if (!is_file($fileDir)) {
            file_put_contents($fileDir, $content);
        }

        return "/${month}/" . rtrim($title, '.md');
    }
}
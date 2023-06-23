<?php

namespace PixlMint\JournalPlugin\Controllers;

use DateTime;
use Nacho\Controllers\AbstractController;
use Nacho\Models\Request;
use PixlMint\CMS\Helpers\CustomUserHelper;
use PixlMint\JournalPlugin\Models\RaceReport;

class AdminController extends AbstractController
{
    public function editCurrent(): string
    {
        $file = $this->getCurrentFile();

        return $this->json(['entryId' => rtrim($file, '.md')]);
    }

    public function uploadRaceReport(Request $request): bool|string
    {
        if (!key_exists('token', $request->getBody()) || !key_exists('raceReport', $request->getBody()) || !key_exists('entry', $request->getBody())) {
            return $this->json(['message' => 'You need to provide a token, raceReport and the entry'], 400);
        }
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You need to be authenticated'], 401);
        }
        $raceReport = new RaceReport($request->getBody()['raceReport']);
        $entry = $this->nacho->getMarkdownHelper()->getPage($request->getBody()['entry']);
        if (!$entry) {
            $this->createSpecific();
        }
        $this->nacho->getMarkdownHelper()->editPage($entry->id, '', ['raceReport' => (array) $raceReport]);

        return $this->json(['message' => 'Successfully stored Race Report']);
    }

    function createSpecific(): string
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
        $folderDir = $_SERVER['DOCUMENT_ROOT'] . "/content/${month}";
        $fileDir = "${folderDir}/${title}";
        // check if file exists, if not create it
        $content =
            "---\ntitle: " .
            rtrim($title, '.md') .
            "\ndate: " .
            $dateEntry->format('Y-m-d H:i') .
            "\n---\n";
        if (!is_dir($folderDir)) {
            mkdir($folderDir);
        }
        if (!is_file($fileDir)) {
            file_put_contents($fileDir, $content);
        }

        return "/${month}/" . rtrim($title, '.md');
    }
}
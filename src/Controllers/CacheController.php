<?php

namespace PixlMint\JournalPlugin\Controllers;

use Nacho\Controllers\AbstractController;
use PixlMint\JournalPlugin\Helpers\CacheHelper;

class CacheController extends AbstractController
{
    public function buildCache()
    {
        if (!$this->isGranted('ROLE_EDITOR')) {
            return $this->json(['message' => 'You need to be authenticated to do this'], 401);
        }
        $cacheHelper = new CacheHelper($this->nacho);
        $cacheHelper->build();

        return $this->json(['success' => true]);
    }
}
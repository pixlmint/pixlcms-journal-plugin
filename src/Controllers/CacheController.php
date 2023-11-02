<?php

namespace PixlMint\JournalPlugin\Controllers;

use Nacho\Controllers\AbstractController;
use Nacho\Models\HttpResponse;
use PixlMint\CMS\Helpers\CustomUserHelper;
use PixlMint\JournalPlugin\Helpers\CacheHelper;

class CacheController extends AbstractController
{
    public function buildCache(): HttpResponse
    {
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You need to be authenticated to do this'], 401);
        }
        $cacheHelper = new CacheHelper($this->nacho);
        $cacheHelper->build();

        return $this->json(['success' => true]);
    }
}
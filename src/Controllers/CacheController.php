<?php

namespace PixlMint\JournalPlugin\Controllers;

use Nacho\Controllers\AbstractController;
use Nacho\Models\HttpResponse;
use PixlMint\CMS\Helpers\CustomUserHelper;
use PixlMint\JournalPlugin\Helpers\CacheHelper;

class CacheController extends AbstractController
{
    /** GET: /api/admin/build-cache */
    public function buildCache(CacheHelper $cacheHelper): HttpResponse
    {
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You need to be authenticated to do this'], 401);
        }
        $cacheHelper->build();

        return $this->json(['success' => true]);
    }
}

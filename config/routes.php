<?php

use PixlMint\JournalPlugin\Controllers\AdminController;
use PixlMint\JournalPlugin\Controllers\CacheController;
use PixlMint\JournalPlugin\Controllers\EntriesController;

return [
    [
        'route' => '/api/entries',
        'controller' => EntriesController::class,
        'function' => 'loadEntries',
    ],
    [
        'route' => '/api/admin/build-cache',
        'controller' => CacheController::class,
        'function' => 'buildCache',
    ],
    [
        'route' => '/api/admin/entry/edit/current',
        'controller' => AdminController::class,
        'function' => 'editCurrent',
    ],
    [
        "route" => "/api/admin/entry/edit",
        "controller" => AdminController::class,
        "function" => "edit"
    ],
    [
        'route' => '/api/admin/cache/build',
        'controller' => AdminController::class,
        'function' => 'buildCache',
    ],
];
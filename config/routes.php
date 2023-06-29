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
        'route' => '/api/admin/entry/race-report',
        'controller' => AdminController::class,
        'function' => 'uploadRaceReport',
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
        'route' => '/api/journal/month',
        'controller' => EntriesController::class,
        'function' => 'loadMonth',
    ],
    [
        'route' => '/api/journal/list-months',
        'controller' => EntriesController::class,
        'function' => 'getMonthsList',
    ],
];
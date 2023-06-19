<?php

use PixlMint\JournalPlugin\Controllers\CacheController;
use PixlMint\JournalPlugin\Controllers\EntriesController;

return [
    'routes' => [
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
    ],
    'journal' => require_once('journal.php'),
    'hooks' => [
        [
            'anchor' => 'init',
            'hook' => \PixlMint\JournalPlugin\Hooks\InitHook::class,
        ],
    ]
];
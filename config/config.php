<?php

return [
    'routes' => require_once('routes.php'),
    'journal' => [
        'year' => 1998,  // Should be overridden by implementations of this plugin
        'version' => '2025.1',
    ],
    'hooks' => [
        [
            'anchor' => 'init',
            'hook' => \PixlMint\JournalPlugin\Hooks\InitHook::class,
        ],
    ]
];

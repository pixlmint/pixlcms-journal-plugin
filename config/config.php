<?php

return [
    'routes' => require_once('routes.php'),
    'journal' => require_once('journal.php'),
    'hooks' => [
        [
            'anchor' => 'init',
            'hook' => \PixlMint\JournalPlugin\Hooks\InitHook::class,
        ],
    ]
];
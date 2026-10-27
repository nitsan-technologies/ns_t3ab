<?php

$EM_CONF['ns_t3ab'] = [
    'title' => 'T3AB – TYPO3 AI Builder',
    'description' => 'T3AB helps you build TYPO3 content blocks, and AI-powered forms, fast and customizable. Just describe your idea, and T3AB generates clean, ready-to-use TYPO3 code with full flexibility.',
    'category' => 'be',
    'author' => 'Team T3Planet',
    'author_email' => 'support@t3planet.com',
    'author_company' => 'T3Planet',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.99.99',
            'php' => '8.2.0-8.5.99',
            'ns_license' => '14.3.0-14.9.99',
            'form' => '12.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

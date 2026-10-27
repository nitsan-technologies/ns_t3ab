<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    't3ab_module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_t3ab/Resources/Public/Icons/parent-module.svg',
    ],
    't3ab_dashboard' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_t3ab/Resources/Public/Icons/t3ab-dashboard.svg',
    ],
    't3ab_forms' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_t3ab/Resources/Public/Icons/t3ab-forms.svg',
    ],
    't3ab_magic' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_t3ab/Resources/Public/Icons/t3ab-magic.svg',
    ],
    't3ab_magic_black' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_t3ab/Resources/Public/Icons/t3ab-magic-black.svg',
    ],
    't3ab_loading' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_t3ab/Resources/Public/Icons/t3ab-loading.svg',
    ],
    't3ab_content_blocks' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_t3ab/Resources/Public/Icons/t3ab-content-blocks.svg',
    ],
];

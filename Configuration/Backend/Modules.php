<?php

use NITSAN\NsT3Ab\Controller\ContentBlocksModuleController;
use NITSAN\NsT3Ab\Controller\ContentBlocksAvailabilityController;
use TYPO3\CMS\Core\Information\Typo3Version;

$languageFile = 'LLL:EXT:ns_t3ab/Resources/Private/Language/locallang_be.xlf';
$isV14OrHigher = (new Typo3Version())->getMajorVersion() >= 14;

if (class_exists('TYPO3\CMS\ContentBlocks\Builder\ContentBlockBuilder')) {
    $controllerActions = [
        ContentBlocksModuleController::class => 'index',
    ];
} else {
    $controllerActions = [
        ContentBlocksAvailabilityController::class => 'contentBlocksNotInstalled',
    ];
}

$contentBlocksModule = [
    'access' => 'user',
    'position' => 'bottom',
    'path' => '/nst3ab/content-blocks',
    'labels' => [
        'title' => $languageFile . ':module.content_blocks',
    ],
    'parent' => 'tools',
    'workspaces' => '*',
    'extensionName' => 'NsT3ab',
    'controllerActions' => $controllerActions,
];

if ($isV14OrHigher) {
    $contentBlocksModule['icon'] = 'EXT:ns_t3ab/Resources/Public/Icons/t3ab-content-blocks-v14.svg';
} else {
    $contentBlocksModule['iconIdentifier'] = 't3ab_content_blocks';
}

return [
    'nst3ab_content_blocks' => $contentBlocksModule,
];

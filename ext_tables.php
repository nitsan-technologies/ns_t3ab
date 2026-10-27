<?php

defined('TYPO3') || die();

(static function () {
    $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
    $pageRenderer->addCssFile('EXT:ns_t3ab/Resources/Public/Css/backend.css');
    $pageRenderer->addInlineLanguageLabelFile('EXT:ns_t3ab/Resources/Private/Language/locallang_be.xlf');
})();

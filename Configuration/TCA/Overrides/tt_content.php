<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content', // table
    'CType', // typeField
    't3ab_content_blocks', // group
    'LLL:EXT:ns_t3ab/Resources/Private/Language/locallang_be.xlf:aiContentElement.groupName', // label
    'before:default', // position
);
<?php

defined('TYPO3') || die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['t3ab'] = [
    'NITSAN\NsT3Ab\ViewHelpers',
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    'module.tx_form {
        view {
            templateRootPaths.78 = EXT:ns_t3ab/Resources/Private/TemplateOverrides/Form/Templates/
            templateRootPaths.10 = EXT:form/Resources/Private/Backend/Templates/
        }
    }'
);
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'] = [
    78 => 'EXT:ns_t3ab/Resources/Private/Templates/Email/',
];

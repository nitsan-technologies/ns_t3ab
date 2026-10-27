<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\ViewHelpers;

use NITSAN\NsT3Ab\Utility\HelperUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ExtConfigViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'key',
            'string',
            '',
            false
        );
    }

    public function render(): string
    {
        $key = $this->arguments['key'];
        $config = HelperUtility::getExtensionGlobalSettings();
        return $config[$key] ?? '';
    }
}

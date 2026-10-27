<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IconNameFormatViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'icon-identifier',
            'string',
            '',
            true
        );
    }

    public function render(): string
    {
        $iconIdentifier = $this->arguments['icon-identifier'];
        $explodeName = GeneralUtility::trimExplode('-', $iconIdentifier, true);
        $name = '';
        if (!empty($explodeName)) {
            foreach ($explodeName as $val) {
                $name .= ucfirst($val);
                if ($val !== end($explodeName)) {
                    $name .= ' ';
                }
            }
        }
        return $name;
    }
}

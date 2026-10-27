<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\ViewHelpers;

use NITSAN\NsT3Ab\ExtensionManager\AiEngineConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetAiEngineViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'engineType',
            'string',
            'AI Engine Type',
            true,
        );
        $this->registerArgument(
            'engineName',
            'string',
            'AI Engine Name',
            false,
        );
    }

    public function render(): array
    {
        $aiEngineType = $this->arguments['engineType'];
        $aiEngineName = $this->arguments['engineName'];
        $aiModelSettings = GeneralUtility::makeInstance(AiEngineConfiguration::class);
        $returnValue = [];
        if ($aiEngineName === '' || $aiEngineName === null) {
            switch ($aiEngineType) {
                case 'text':
                    $returnValue = $aiModelSettings->getTextGenerationAIEngines();
                    break;
                case 'image':
                    $returnValue = $aiModelSettings->getImageGenerationAIEngines();
                    break;
                case 'audio':
                    $returnValue = $aiModelSettings->getAudioGenerationAIEngines();
                    break;
            }
        } else {
            if (
                $aiEngineType === 'text' &&
                isset($aiModelSettings->getTextGenerationAIEngines()[$aiEngineName])
            ) {
                $returnValue = $aiModelSettings->getTextGenerationAIEngines()[$aiEngineName];
            }
            if (
                $aiEngineType === 'audio' &&
                isset($aiModelSettings->getAudioGenerationAIEngines()[$aiEngineName])
            ) {
                $returnValue = $aiModelSettings->getAudioGenerationAIEngines()[$aiEngineName];
            }
        }

        return $returnValue;
    }
}

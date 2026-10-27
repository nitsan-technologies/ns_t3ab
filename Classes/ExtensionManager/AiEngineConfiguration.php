<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\ExtensionManager;

use NITSAN\NsT3Ab\Utility\HelperUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AiEngineConfiguration
{
    private $textGenerationAIEngines = [
      'openai' => [
        'gpt-4.1',
        'gpt-4o',
        'gpt-5',
        'gpt-5.6-terra',
        'gpt-5.6-sol',
        'gpt-6-luna',
      ],
      'gemini' => [
        'gemini-3.5-flash-lite',
        'gemini-3.8-flash',
      ],
      'azure' => [],
      'claude' => [
        'claude-haiku-5-5',
        'claude-sonnet-5-5',
        'claude-opus-4-8',
      ],
      'deepseek' => [
        'deepseek-flash',
        'deepseek-v4-pro',
      ],
      'xai' => [
        'grok-2-latest',
        'grok-2-vision-latest',
      ],
    ];

    private $imageGenerationAIEngines = [
      'dalle',
      'gpt-image-1',
      'stabilityai',
      'midjourneyai',
      'unsplash',
      'openverse',
      'pixabay',
      'pexels',
    ];

    private $audioGenerationAIEngines = [
      'openai' => [
        'tts-1',
        'tts-1-hd',
      ],
      'elevenlabs' => [
        'eleven_multilingual_v2',
        'eleven_turbo_v2_5',
        'eleven_turbo_v2',
        'eleven_multilingual_v1',
        'eleven_english_sts_v2',
        'eleven_monolingual_v1',
      ],
    ];

    public ExtensionConfiguration $extensionConfiguration;

    public function __construct()
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

        // Set Azure Domain From Extension Configuration
        $this->textGenerationAIEngines['azure'] = [
          $this->getAzureDomain(),
        ];
        if (isset($this->extensionConfiguration->get('ns_t3ab')['enable_ollama_model'])) {
            $this->textGenerationAIEngines['ollama'] = $this->getOllamaDomain();
        }
    }

    private function getAzureDomain(): string
    {
        $extensionConfiguration = HelperUtility::getExtensionGlobalSettings();

        return isset($extensionConfiguration['azure_api_model'])  &&
          $extensionConfiguration['azure_api_model'] !== '' ?
          $extensionConfiguration['azure_api_model'] : 'Default';
    }

    private function getOllamaDomain(): array
    {
        if (
            isset($this->extensionConfiguration->get('ns_t3ab')['ollama_api_model']) &&
            !empty($this->extensionConfiguration->get('ns_t3ab')['ollama_api_model'])
        ) {
            $ollamaApiModels = GeneralUtility::trimExplode(',', $this->extensionConfiguration->get('ns_t3ab')['ollama_api_model']);
            return (isset($ollamaApiModels[0]) && !empty($ollamaApiModels[0])) ? $ollamaApiModels : [];
        } else {
            return [];
        }
    }

    public function getTextGenerationAIEngines(bool $ignoreConfig = false): array
    {
        return $this->filterEnginesByConfig($this->textGenerationAIEngines, false, $ignoreConfig);
    }

    public function getImageGenerationAIEngines(): array
    {
        return $this->filterEnginesByConfig($this->imageGenerationAIEngines, true);
    }

    public function getAudioGenerationAIEngines(): array
    {
        return $this->audioGenerationAIEngines;
    }

    public function getAllAIEngines(): array
    {
        return array_merge(
            $this->textGenerationAIEngines,
            $this->imageGenerationAIEngines,
            $this->audioGenerationAIEngines
        );
    }

    /**
     * Customize for usage in AI Log module
     * @return array
     */
    public function getAllEngines()
    {
        $aiEngines = [];
        foreach ($this->textGenerationAIEngines as $key => $textModule) {
            $aiEngines[$key] = $key;
        }
        foreach ($this->imageGenerationAIEngines as $key => $imageModules) {
            $aiEngines[$imageModules] = $imageModules;
        }
        foreach ($this->audioGenerationAIEngines as $key => $audioModules) {
            $aiEngines[$key] = $key;
        }
        return $aiEngines;
    }

    /**
     * Customize for the usage of AI Log Module
     */
    public function getTextGenerateEngines()
    {
        $aiEngines = [
          'mainModules' => [],
          'allModules' => $this->textGenerationAIEngines,
        ];
        $mainModules = [];
        foreach ($this->textGenerationAIEngines as $key => $engines) {
            $mainModules[$key] = $key;
        }
        array_push($aiEngines['mainModules'], $mainModules);
        return $aiEngines;
    }

    /**
     * Filters the given AI engines based on the extension configuration.
     *
     * @param array $engines
     * @param bool $useValueAsKey If true, the array value is used as the map key instead of the array key.
     * @return array
     */
    private function filterEnginesByConfig(array $engines, bool $useValueAsKey = false, bool $ignoreConfig = false): array
    {
        $extensionConfiguration = HelperUtility::getExtensionGlobalSettings();
        $mappedEngines = $this->mapEngines();
        if ($ignoreConfig) {
            return $engines;
        }
        foreach ($engines as $key => $value) {
            $mapKey = $useValueAsKey ? $value : $key;
            if (empty($extensionConfiguration[$mappedEngines[$mapKey] ?? ''])) {
                unset($engines[$key]);
            }
        }
        return $engines;
    }

    /**
     * Map the engines to the extension configuration keys
     * @return array
     */
    private function mapEngines(): array
    {
        return [
          'azure' => 'azure_api_key',
          'claude' => 'claude_api_key',
          'openai' => 'openai_api_key',
          'gemini' => 'gemini_api_key',
          'deepseek' => 'deepseek_api_key',
          'xai' => 'xai_api_key',
          'ollama' => 'ollama_api_key',
          'deepl' => 'deepl_api_key',
          'google' => 'google_api_key',
          'unsplash' => 'unsplashAccessKey',
          'stabilityai' => 'stabilityAiApiKey',
          'pixabay' => 'pixabayAccessKey',
          'pexels' => 'pexelsAccessKey',
          'midjourneyai' => 'midjourneyApiKey',
          'dalle' => 'openai_api_key',
          'openverse' => 'openverse_api_key',
        ];
    }
}

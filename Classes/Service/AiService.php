<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Service;

use NITSAN\NsT3Ab\Client\BaseClient;
use TYPO3\CMS\Core\Http\RequestFactory;
use NITSAN\NsT3Ab\Utility\HelperUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AiService
{
    protected array $extConf = [];

    protected BaseClient $baseClient;

    protected RequestFactory $requestFactory;

    public function __construct()
    {
        $this->extConf = HelperUtility::getExtensionGlobalSettings();
        $this->baseClient = GeneralUtility::makeInstance(BaseClient::class);
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
    }

    public function requestAi(
        string $prompt,
        string $type,
        array $parsedBody
    )
    {
        $jsonContent = [
            "model" => $this->extConf['openai_model'],
            "temperature" => (float)$this->extConf['openai_temperature'],
            "max_tokens" => (int)$this->extConf['openai_max_tokens'],
            "top_p" => (float)$this->extConf['openai_top_p'],
            "frequency_penalty" => (float)$this->extConf['openai_frequency_penalty'],
            "presence_penalty" => (float)$this->extConf['openai_presence_penalty']
        ];
        switch ($type) {
            case 'AiForm' :
                $this->addAiFormPrompt($jsonContent, $prompt);
                break;
            case 'AiContentBlocks' :
                $this->addAiContentBlocksPrompt($jsonContent, $prompt);
                break;
            default:
                $jsonContent["prompt"] = $prompt;
                break;
        }
        $aiModel = $parsedBody['text-api-model'];
        $aiSelectedModel = $parsedBody['aiSelectedModel'];
        $requestData = $this->baseClient->getRequestData($aiModel, $jsonContent, '', $aiSelectedModel);
        $response = $this->requestFactory->request(
            $requestData['url'],
            'POST',
            $requestData['body']
        );
        $resJsonBody = $response->getBody()->getContents();
        $resBody = json_decode($resJsonBody, true);
        $generatedText = $this->baseClient->getResponseData($aiModel, $resBody);
        return ltrim($generatedText);
    }

    /**
     * @param array $jsonContent
     * @param string $prompt
     * @return void
     */
    protected function addAiFormPrompt(array &$jsonContent, string $prompt): void
    {
        if ($this->isChatGptRequest()) {
            $jsonContent["messages"][] = [
                'role' => 'user',
                'content' => 'Create a TYPO3 form structure in JSON format for "' . $prompt . '". Include the following elements type, identifier, label, placeholder and required. Do not include any other text or comments. Must follow this response format is form->fields.'
            ];
        } else {
            $jsonContent["prompt"] = 'Create a TYPO3 form structure in JSON format for "' . $prompt . '". Include the following elements type, identifier, label, placeholder and required';
        }
    }

    /**
     * @param array $jsonContent
     * @param string $prompt
     * @return void
     */
    protected function addAiContentBlocksPrompt(array &$jsonContent, string $prompt): void
    {
        if ($this->isChatGptRequest()) {
            $jsonContent["messages"][] = [
                'role' => 'user',
                'content' => 'Create a TYPO3 form structure in JSON format for "' . $prompt . '". Include the following elements type, identifier, label, placeholder and required. Do not include any other text or comments. Must follow this response format is form->fields.'
            ];
        } else {
            $jsonContent["prompt"] = 'Create a TYPO3 form structure in JSON format for "' . $prompt . '". Include the following elements type, identifier, label, placeholder and required';
        }
    }

    /**
     * @return boolean
     */
    private function isChatGptRequest(): bool
    {
        return $this->extConf['openai_model'] === 'gpt-4.1' ||
            $this->extConf['openai_model'] === 'gpt-4o' ||
            $this->extConf['openai_model'] === 'gpt-5' ||
            $this->extConf['openai_model'] === 'gpt-5.6-terra' ||
            $this->extConf['openai_model'] === 'gpt-5.6-sol' ||
            $this->extConf['openai_model'] === 'gpt-6-luna';
    }
}
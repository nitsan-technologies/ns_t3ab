<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Client;

use DateTime;
use Exception;
use NITSAN\NsT3Ab\Utility\HelperUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BaseClient
 *
 * Provides functionality to prepare request and response data for various AI providers.
 *
 */
class BaseClient
{
    private const OPENAI_API_URL = 'https://api.openai.com/';
    private const OPENAI_CHAT_ENDPOINT = 'v1/chat/completions';
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private const CLAUDE_API_URL = 'https://api.anthropic.com/v1/messages';
    private const OPENAI_LEGACY_API_URL = 'https://api.openai.com/v1/completions';
    private const OPENAI_USAGE_ENDPOINT = 'v1/usage';
    private const DEEPSEEK_API_URL = 'https://api.deepseek.com/';
    private const DEEPSEEK_CHAT_ENDPOINT = 'chat/completions';
    private const XAI_API_URL = 'https://api.x.ai/';
    private const XAI_CHAT_ENDPOINT = 'v1/chat/completions';

    protected bool $nonLegacyModel;

    /**
     * @var RequestFactory
     */
    protected RequestFactory $requestFactory;

    /**
     * @var array
     */
    protected array $extConf;

    public function __construct()
    {
        $this->extConf = HelperUtility::getExtensionGlobalSettings();
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $this->nonLegacyModel = $this->checkSelectedModel($this->extConf);
    }

    /**
     * @param string $type
     * @param array $data
     * @param string $content
     * @param string $aiSelectedModel
     * @return array
     */
    public function getRequestData(string $type, array $data, string $content = '', $aiSelectedModel = ''): array
    {
        $requestData = [];
        switch ($type) {
            case 'openai':
                $requestData = $this->getOpenAiRequestData($data, $aiSelectedModel);
                break;
            case 'gemini':
                $requestData = $this->getGeminiRequestData($data, $content, $aiSelectedModel);
                break;
            case 'azure':
                $requestData = $this->getAzureRequestData($data, $content, $aiSelectedModel);
                break;
            case 'claude':
                $requestData = $this->getClaudeRequestData($data, $content, $aiSelectedModel);
                break;
            case 'ollama':
                $requestData = $this->getOllamaRequestData($content, $aiSelectedModel);
                break;
            case 'deepseek':
                $requestData = $this->getDeepseekRequestData($aiSelectedModel);
                break;
            case 'xai':
                $requestData = $this->getXaiRequestData($data, $aiSelectedModel);
                break;
        }
        return $requestData;
    }

    /**
     * @param string $type
     * @param array $responseArray
     * @return string
     */
    public function getResponseData(string $type, array $responseArray): string
    {
        $generatedText = '';
        switch ($type) {
            case 'openai':
                $generatedText = $this->getOpenAiResponseData($responseArray);
                break;
            case 'gemini':
                $generatedText = $this->getGeminiResponseData($responseArray);
                break;
            case 'azure':
                $generatedText = $this->getAzureResponseData($responseArray);
                break;
            case 'claude':
                $generatedText = $this->getClaudeResponseData($responseArray);
                break;
            case 'ollama':
                $generatedText = $this->getOllamaResponseData($responseArray);
                break;
            case 'deepseek':
                $generatedText = $this->getDeepseekResponseData($responseArray);
                break;
            case 'xai':
                $generatedText = $this->getXaiResponseData($responseArray);
                break;
        }

        return $generatedText;
    }

    /**
     * @param array $data
     * @param string $aiSelectedModel
     * @return array
     */
    protected function getOpenAiRequestData(array $data, string $aiSelectedModel): array
    {
        $requestData = [];
        $requestData['url'] = $this->nonLegacyModel ? self::OPENAI_API_URL . self::OPENAI_CHAT_ENDPOINT : self::OPENAI_LEGACY_API_URL;
        $data['model'] = !empty($aiSelectedModel) ? $aiSelectedModel : $this->extConf['openai_model'];

        if (in_array($data['model'], ['gpt-5', 'gpt-5.6-terra', 'gpt-5.6-sol', 'gpt-6-luna'], true)) {
            $data['max_completion_tokens'] = $data['max_tokens'];
            unset(
                $data['max_tokens'],
                $data['temperature'],
                $data['top_p'],
                $data['presence_penalty'],
                $data['frequency_penalty']
            );
        }

        $requestData['body'] = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->extConf['openai_api_key'],
            ],
            'json' => $data,
        ];
        return $requestData;
    }

    /**
     * @param string $content
     * @param string $aiSelectedModel
     * @return array
     */
    public function getOllamaRequestData(string $content, string $aiSelectedModel): array
    {
        $requestData = [];
        $model = !empty($aiSelectedModel) ? $aiSelectedModel : $this->extConf['ollama_api_model'];

        // Determine the endpoint type from the API URL
        $endpointType = strpos($this->extConf['ollama_api_url'], '/api/chat') !== false ? 'chat' : 'generate';

        // Base request body data
        $requestBodyData = [
            'model' => $model,
            'stream' => false,
        ];

        // Add endpoint-specific data structure
        if ($endpointType === 'chat') {
            $requestBodyData['messages'] = [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ];
        } else {
            $requestBodyData['prompt'] = $content;
        }

        $ollamaOptions = [
            'temperature',
            'top_k',
            'top_p',
            'num_predict',
        ];

        foreach ($ollamaOptions as $opion) {
            if (isset($this->extConf['ollama_' . $opion]) && $this->extConf['ollama_' . $opion] != '') {
                $requestBodyData[$opion] = $this->extConf['ollama_' . $opion];
            }
        }

        $requestData['body'] = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($requestBodyData),
        ];
        $requestData['url'] = $this->extConf['ollama_api_url'];

        return $requestData;
    }

    /**
     *
     *
     * @param array $data
     * @param string $content
     * @param string $aiSelectedModel
     * @return array
     */
    protected function getGeminiRequestData(array $data, string $content, string $aiSelectedModel): array
    {
        $requestData = [];
        $requestPayload = [
            'contents' => [
                'parts' => [
                    ['text' => $data['messages'][0]['content'] ?? $content],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 8192,
                'stopSequences' => [],
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_NONE',
                ],
            ],
        ];

        $model = !empty($aiSelectedModel) ? $aiSelectedModel : $this->extConf['gemini_model'];
        $requestData['url'] = self::GEMINI_API_URL . $model . ':generateContent';
        $requestData['body'] = [
            'query' => [
                'key' => $this->extConf['gemini_api_key'],
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $requestPayload,
        ];

        return $requestData;
    }

    /**
     * @param array $data
     * @param string $content
     * @param string $aiSelectedModel
     * @return array
     */
    protected function getAzureRequestData(array $data, string $content, string $aiSelectedModel): array
    {
        $model = !empty($aiSelectedModel) ? $aiSelectedModel : $this->extConf['azure_api_model'];
        $requestData['url'] = $this->extConf['azure_api_endpoint'] . 'openai/deployments/' . $model . '/chat/completions';
        $requestData['body'] = [
            'query' => [
                'api-version' => $this->extConf['azure_api_version'],
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'api-key' => $this->extConf['azure_api_key'],
            ],
            'json' => [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $data['messages'][0]['content'] ?? $content,
                    ],
                ],
            ],
        ];
        return $requestData;
    }

    /**
     * @param array $data
     * @param string $content
     * @param string $aiSelectedModel
     * @return array
     */
    protected function getClaudeRequestData(array $data, string $content, string $aiSelectedModel): array
    {
        $model = !empty($aiSelectedModel) ? $aiSelectedModel : $this->extConf['claude_api_model'];
        $requestPayload = [
            'model' => $model,
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $data['messages'][0]['content'] ?? $content,
                ],
            ],
        ];

        $requestData['url'] = self::CLAUDE_API_URL;
        $requestData['body'] = [
            'headers' => [
                'x-api-key' => $this->extConf['claude_api_key'],
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => $requestPayload,
        ];

        return $requestData;
    }

    /**
     * @param string $aiSelectedModel
     * @return array
     */
    protected function getDeepseekRequestData(string $aiSelectedModel): array
    {
        $requestData = [
            'url' => self::DEEPSEEK_API_URL . self::DEEPSEEK_CHAT_ENDPOINT,
            'body' => [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->extConf['deepseek_api_key'],
                ],
                'json' => [
                    'model' => $aiSelectedModel ?: $this->extConf['deepseek_model'],
                    'stop' => $this->extConf['deepseek_stop'] ?? null,
                    'response_format' => [
                        'type' => $this->extConf['deepseek_response_format'] ?? 'text',
                    ],
                    'stream' => (bool)($this->extConf['deepseek_stream'] ?? false),
                    'stream_options' => $this->extConf['deepseek_stream_options'] ?? null,
                    'tools' => $this->extConf['deepseek_tools'] ?? null,
                    'tool_choice' => $this->extConf['deepseek_tool_choice'] ?? 'none',
                    'logprobs' => (bool)($this->extConf['deepseek_logprobs'] ?? false),
                    'top_logprobs' => $this->extConf['deepseek_top_logprobs'] ?? null,
                ],
            ],
        ];

        return $requestData;
    }

    /**
     * @param array $data
     * @param string $aiSelectedModel
     * @return array
     */
    protected function getXaiRequestData(array $data, string $aiSelectedModel): array
    {
        $requestData = [
            'url' => self::XAI_API_URL . self::XAI_CHAT_ENDPOINT,
            'body' => [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->extConf['xai_api_key'],
                ],
                'json' => [
                    'model' => $aiSelectedModel ?: $this->extConf['xai_model'],
                    'messages' => $data['messages'],
                    'response_format' => [
                        'type' => $this->extConf['xai_response_format'] ?? 'text',
                    ],
                    'temperature' => (float)$this->extConf['xai_temperature'] ?? null,
                    'max_tokens' => (int)$this->extConf['xai_max_tokens'] ?? null,
                ],
            ],
        ];
        return $requestData;
    }

    /**
     * @param array $responseArray
     * @return string
     */
    protected function getOpenAiResponseData($responseArray): string
    {
        return
            $this->extConf['openai_model'] === 'gpt-4.1' ||
            $this->extConf['openai_model'] === 'gpt-4o' ||
            $this->extConf['openai_model'] === 'gpt-5' ||
            $this->extConf['openai_model'] === 'gpt-5.6-terra' ||
            $this->extConf['openai_model'] === 'gpt-5.6-sol' ||
            $this->extConf['openai_model'] === 'gpt-6-luna' ?
            $responseArray['choices'][0]['message']['content'] : $responseArray['choices'][0]['text'];
    }

    /**
     * @param array $responseArray
     * @return string
     */
    protected function getOllamaResponseData(array $responseArray): string
    {
        $endpointType = strpos($this->extConf['ollama_api_url'], '/api/chat') !== false ? 'chat' : 'generate';
        if ($endpointType === 'chat') {
            return $responseArray['message']['content'];
        } else {
            return $responseArray['response'];
        }
    }

    /**
     * @param array $responseArray
     * @return string
     */
    protected function getGeminiResponseData(array $responseArray): string
    {
        $generatedText = '';
        if (isset($responseArray['candidates'])) {
            $generatedText = isset($responseArray['candidates'][0]['content']) ? $responseArray['candidates'][0]['content']['parts'][0]['text'] : '';
        } elseif (isset($responseArray['error']['code']) && $responseArray['error']['code'] == 400) {
            $generatedText = $responseArray['error']['code'];
        }
        return $generatedText;
    }

    /**
     * @param array $responseArray
     * @return string
     */
    protected function getAzureResponseData(array $responseArray): string
    {
        return  $responseArray['choices'][0]['message']['content'] ?? $responseArray['choices'][0]['text'];
    }

    /**
     * @param array $responseArray
     * @return string
     */
    protected function getClaudeResponseData(array $responseArray): string
    {
        $generatedText = '';
        if (isset($responseArray['content'][0]['text'])) {
            $generatedText = $responseArray['content'][0]['text'];
            $substring = ':';
            // Find the position of the first occurrence of the substring
            $position = strpos($generatedText, $substring);

            // Check if the substring is found in the string
            if ($position !== false) {
                // Calculate the position right after the substring
                $startPosition = $position + strlen($substring);
                // Extract the part of the string after the substring
                $generatedText = substr($generatedText, $startPosition);
            }
        } elseif (isset($responseArray['error']['code']) && $responseArray['error']['code'] == 400) {
            $generatedText = $responseArray['error']['code'];
        } elseif (isset($responseArray['error'])) {
            $generatedText = $responseArray['error']['message'];
        }
        return $generatedText;
    }

    /**
     * @param string $date
     * @param integer $dateScope
     * @return array
     */
    public function getOpenAiUsageData(string $date, int $dateScope): array
    {
        if ($date == '') {
            $date = date('Y-m-d');
        }

        if ($dateScope === 0) {
            $url = self::OPENAI_API_URL . self::OPENAI_USAGE_ENDPOINT . '?date=' . $date;
            return $this->getOpenAiUsageDataFromApi($url);
        } else {
            $returnData = [];
            for ($i = 0; $i <= $dateScope; $i++) {
                $date = new DateTime();
                $date->modify('-' . ($i) . 'days');
                $newDate = $date->format('Y-m-d');
                $url = self::OPENAI_API_URL . self::OPENAI_USAGE_ENDPOINT . '?date=' . $newDate;
                $currentData = $this->getOpenAiUsageDataFromApi($url);
                if (isset($returnData['responseData'])) {
                    if (isset($returnData['responseData']['data']) && isset($currentData['responseData']['data'])) {
                        $returnData['responseData']['data'] = array_merge($returnData['responseData']['data'], $currentData['responseData']['data']);
                    }
                    if (isset($returnData['responseData']['dalle_api_data']) && isset($currentData['responseData']['dalle_api_data'])) {
                        $returnData['responseData']['dalle_api_data'] = array_merge($returnData['responseData']['dalle_api_data'], $currentData['responseData']['dalle_api_data']);
                    }
                } else {
                    $returnData = $this->getOpenAiUsageDataFromApi($url);
                }
            }
            return $returnData;
        }
    }

    /**
     * @param string $url
     * @return array
     */
    protected function getOpenAiUsageDataFromApi(string $url): array
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $authorization = 'Bearer ' . $this->extConf['openai_api_key'];
        try {
            $response = $requestFactory->request(
                $url,
                'GET',
                [
                    'headers' => [
                        'Authorization' => $authorization,
                    ],
                ]
            );
            $resJsonBody = $response->getBody()->getContents();
            return [
                'success' => true,
                'responseData' => json_decode($resJsonBody, true),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'responseData' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param array $responseArray
     * @return string
     */
    protected function getDeepseekResponseData(array $responseArray): string
    {
        return $responseArray['choices'][0]['message']['content'] ?? '';
    }

    /**
     * @param array $responseArray
     * @return string
     */
    protected function getXaiResponseData(array $responseArray): string
    {
        return $responseArray['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Check if the selected model is valid
     *
     * @param $extConf
     * @return boolean
     */
    public function checkSelectedModel($extConf): bool
    {
        if (!is_array($extConf) || !isset($extConf['openai_model'])) {
            return false;
        }

        return $extConf['openai_model'] === 'gpt-4o' ||
            $extConf['openai_model'] === 'gpt-4.1' ||
            $extConf['openai_model'] === 'gpt-5' ||
            $extConf['openai_model'] === 'gpt-5.6-terra' ||
            $extConf['openai_model'] === 'gpt-5.6-sol' ||
            $extConf['openai_model'] === 'gpt-6-luna';
    }
}

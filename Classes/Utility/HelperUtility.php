<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Class HelperUtility
 *
 * @extensionScannerIgnoreFile
 */
class HelperUtility
{
    /**
     * @return array
     */
    public static function getExtensionGlobalSettings(): array
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('ns_t3ab');
    }

    /**
     * @return int
     */
    public static function getCurrentTypo3Version(): int
    {
        return (int)VersionNumberUtility::convertVersionStringToArray(
            VersionNumberUtility::getCurrentTypo3Version()
        )['version_main'];
    }

    /**
     * @param string $type
     */
    public static function getSeverityClass(string $type)
    {
        switch ($type) {
            case 'ERROR':
                return \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR;
            case 'WARNING':
                return \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING;
            default:
                return \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::OK;
        }
    }

    /**
     * @param string $request
     * @return string
     */
    public static function getSelectedModal(array $request): string
    {
        $result = '';
        if (isset($request['text-api-model'])) {
            switch ($request['text-api-model']) {
                case 'gemini':
                    $result = $request['gemini-models'] ?? '';
                    break;
                    case 'openai':
                        $result = $request['openai-models'] ?? '';
                    break;
                case 'azure':
                    $result = $request['azure-models'] ?? '';
                    break;
                case 'claude':
                    $result = $request['claude-models'] ?? '';
                    break;
            }
        }
        return $result;
    }

    /**
     * Decode AI model output that may include markdown fences or surrounding prose.
     *
     * @return array<string, mixed>|null
     */
    public static function parseAiJsonResponse(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $raw, $matches) === 1) {
            $raw = trim($matches[1]);
        } elseif (preg_match('/```(?:json)?\s*(.*?)\s*```/is', $raw, $matches) === 1) {
            $raw = trim($matches[1]);
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $raw, $matches) === 1) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
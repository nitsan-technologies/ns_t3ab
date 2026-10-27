<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Controller;

use GuzzleHttp\Exception\ClientException;
use NITSAN\NsT3Ab\Service\PackageResolverService;
use NITSAN\NsT3Ab\Utility\ContentBlocksIconUtility;
use NITSAN\NsT3Ab\Utility\ContentBlocksYamlConfig;
use NITSAN\NsT3Ab\Utility\HelperUtility;
use Normalizer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockBuilder;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Definition\Factory\UniqueIdentifierCreator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Service\OpcodeCacheService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Install\Service\ClearCacheService;

class ContentBlocksModuleController extends AbstractController
{
    /**
     * @var ContentBlockBuilder
     */
    protected ContentBlockBuilder $contentBlockBuilder;

    /**
     * @param ContentBlockBuilder $contentBlockBuilder
     */
    protected ContentBlockRegistry $contentBlockRegistry;

    /**
     * @var ClearCacheService
     */
    protected ClearCacheService $clearCacheService;

    public function __construct(
        ContentBlockBuilder $contentBlockBuilder,
        ContentBlockRegistry $contentBlockRegistry,
        ClearCacheService $clearCacheService
    ) {
        parent::__construct();
        $this->contentBlockBuilder = $contentBlockBuilder;
        $this->contentBlockRegistry = $contentBlockRegistry;
        $this->clearCacheService = $clearCacheService;
    }

    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        $availableContentBlocks = $this->getAvailableContentBlocks();
        $assign = [
            'availableContentBlocks' => $availableContentBlocks,
        ];
        $this->pageRenderer->loadJavaScriptModule('@nitsan/nst3ab/modal-view.js');
        return $this->getViewAndTemplate($this->request, $assign, 'ContentBlocksModule/', 'Index');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function renderContentElementGenerationTemplateAction(ServerRequestInterface $request): ResponseInterface
    {
        $extConf = HelperUtility::getExtensionGlobalSettings();
        $pageId = (int)(isset($request->getQueryParams()['pageId']) ? $request->getQueryParams()['pageId'] : 0);
        $pageTsConfig = BackendUtility::getPagesTSconfig($pageId);
        $contentIcons = ContentBlocksIconUtility::getIcons();
        $wizards = $pageTsConfig['mod.']['wizards.']['newContentElement.']['wizardItems.'] ?? [];
        $contentElementGroup = [];
        foreach ($wizards as $groupKey => $wizardGroup) {
            if (!isset($wizardGroup['header'])) {
                continue;
            }
            $key = rtrim($groupKey, '.');
            if (str_starts_with($wizardGroup['header'] ?? '', 'LLL:EXT')) {
                $localLangLabel = $wizardGroup['header'];
                $normalLabel =  '';
            } else {
                $localLangLabel = '';
                $normalLabel =  $wizardGroup['header'];
            }
            $contentElementGroup[$key] = [
                'localLangLabel' => $localLangLabel,
                'normalLabel' => $normalLabel,
            ];
        }
        $packageResolver = GeneralUtility::makeInstance(PackageResolverService::class);
        $availablePackages = $packageResolver->getAvailablePackages();
        if (isset($extConf['selectPackageToStoreAiContentElements'])) {
            $selectedPackage = $availablePackages[$extConf['selectPackageToStoreAiContentElements']];
        } else {
            $selectedPackage = 'ns_t3ab';
        }
        $assign = [
            'contentIcons' => $contentIcons,
            't3version' => $this->typo3Version,
            'selectedPackage' => $selectedPackage,
            'availablePackages' => $availablePackages,
            'defaultModel' => isset($extConf['defaultModel']) ? $extConf['defaultModel'] : null,
            'extensionConfig' => $extConf,
            'contentElementGroup' => $contentElementGroup,
            'submitAction' => $this->backendUriBuilder->buildUriFromRoute('content_blocks_field_generation', ['pageId' => $pageId]),
        ];
        $this->pageRenderer->loadJavaScriptModule('@nitsan/nst3ab/ai-content-blocks.js');
        return $this->getViewAndTemplate($request, $assign, 'ContentBlocksModule/', 'ContentElementGeneration');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function contentElementFieldPreviewAction(ServerRequestInterface $request): ResponseInterface
    {
        $pageId = (int)$request->getQueryParams()['pageId'] ?? 0;
        $parsedBody = $request->getParsedBody();
        $error = '';
        $validate = true;
        $assign = [];

        if (
            isset($parsedBody['contentBlockKey']) &&
            isset($parsedBody['packageName']) &&
            isset($parsedBody['elementName']) &&
            isset($parsedBody['contentBlockPrompt']) &&
            isset($parsedBody['category']) &&
            isset($parsedBody['elementDescription'])
        ) {
            $vendorName = '';
            $packageResolver = GeneralUtility::makeInstance(PackageResolverService::class);
            $availablePackages = $packageResolver->getAvailablePackages();
            $selectedPackage = $availablePackages[$parsedBody['packageName']];
            $selectedPackageName = $selectedPackage->getValueFromComposerManifest()->name;
            $explodedName = GeneralUtility::trimExplode('/', $selectedPackageName);
            $vendorName = isset($explodedName[0]) ? $explodedName[0] : '';

            if (!$this->isValid($vendorName)) {
                $error = 'Your vendor name does not match the requirement.';
                $validate = false;
            }
            if (!$this->isValid($parsedBody['contentBlockKey'])) {
                $error = 'Your Content Block Key does not match the requirements.';
                $validate = false;
            }
            $extension = $parsedBody['packageName'];
            $base = 'EXT:' . $extension . '/';
            $path = $base . ContentBlockPathUtility::getRelativeContentElementsPath();
            $basePath = GeneralUtility::getFileAbsFileName($path);
            $checkExists = $this->checkContentBlockExists($basePath, $parsedBody['contentBlockKey']);
            if ($checkExists) {
                $error = 'Content Block already exists';
                $validate = false;
            }
            $aiModel = !empty($parsedBody['text-api-model']) ? $parsedBody['text-api-model'] : 'openai';
            $aiSelectedModel = HelperUtility::getSelectedModal($parsedBody);
            $parsedBody['text-api-model'] = $aiModel;
            if (!isset($parsedBody['aiSelectedModel'])) {
                $parsedBody['aiSelectedModel'] = $aiSelectedModel;
            } else {
                $aiSelectedModel = $parsedBody['aiSelectedModel'];
            }
            if ($validate) {
                try {
                    $aiResult = $this->aiService->requestAi(
                        $parsedBody['contentBlockPrompt'],
                        'AiContentBlocks',
                        $parsedBody
                    );
                    $responseArray = HelperUtility::parseAiJsonResponse($aiResult);
                    if (isset($responseArray['form'])) {
                        $responseArray = $responseArray['form'];
                    }
                    $validYamlArray = [];
                    if (isset($responseArray['fields']) && is_array($responseArray['fields'])) {
                        foreach ($responseArray['fields'] as $field) {
                            $validYamlArray[] = ContentBlocksYamlConfig::getFieldConfig($field);
                        }
                    } elseif (isset($responseArray['elements']) && is_array($responseArray['elements'])) {
                        foreach ($responseArray['elements'] as $field) {
                            $validYamlArray[] = ContentBlocksYamlConfig::getFieldConfig($field);
                        }
                    } else {
                        $this->showFlashMessage(
                            $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':t3ab.contentBlock.tryDifferentPrompt'),
                            '',
                            'WARNING'
                        );
                        return new RedirectResponse($this->backendUriBuilder->buildUriFromRoute('ai_content_blocks'));
                    }
                } catch (ClientException $e) {
                    $error = $this->guzzleClientExceptionHandler($e);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        } else {
            $error = $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':error_title');
        }

        if ($error !== '') {
            $this->showFlashMessage(
                $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':t3ab.error'),
                $error,
                'ERROR'
            );
            return new RedirectResponse($this->backendUriBuilder->buildUriFromRoute('ai_content_blocks'));
        } else {
            $assign = [
                'text-api-model' => $parsedBody['text-api-model'],
                'aiSelectedModel' => $parsedBody['aiSelectedModel'],
                'contentBlockKey' => $parsedBody['contentBlockKey'],
                'packageName' => $parsedBody['packageName'],
                'elementName' => $parsedBody['elementName'],
                'contentBlockPrompt' => $parsedBody['contentBlockPrompt'],
                'elementDescription' => $parsedBody['elementDescription'],
                'category' => $parsedBody['category'],
                'cbIcon' => $parsedBody['cbIcon'] ?? '',
                'validYamlArray' => $validYamlArray,
                'validYamlArrayJson' => json_encode($validYamlArray, 1),
                'allowedFields' => ContentBlocksYamlConfig::getContentBlocksFieldTypes(),
                'allowedTextFields' => ContentBlocksYamlConfig::getAllowedTextFields(),
                'allowedFalFields' => ContentBlocksYamlConfig::getAllowedFalFields(),
                'allowedMultiSelectFields' => ContentBlocksYamlConfig::getAllowedMultiSelectFields(),
                'backUrl' => $this->backendUriBuilder->buildUriFromRoute('ai_content_blocks', ['pageId' => $pageId]),
                'reGenerateUrl' => $this->backendUriBuilder->buildUriFromRoute('content_blocks_field_generation', ['pageId' => $pageId]),
                'createUrl' => $this->backendUriBuilder->buildUriFromRoute('create_content_element'),
            ];
        }
        $this->pageRenderer->loadJavaScriptModule('@nitsan/nst3ab/ai-content-blocks.js');
        return $this->getViewAndTemplate($request, $assign, 'ContentBlocksModule/', 'ContentElementFieldPreview');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function createContentElementAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $parsedBody = $request->getParsedBody();
        $error = '';
        $validate = true;
        $success = false;
        $files = [];
        if (
            isset($parsedBody['contentBlockKey']) &&
            isset($parsedBody['packageName']) &&
            isset($parsedBody['elementName']) &&
            isset($parsedBody['contentBlockPrompt']) &&
            isset($parsedBody['validYamlArrayJson']) &&
            isset($parsedBody['elementDescription'])
        ) {
            $vendorName = '';
            $packageResolver = GeneralUtility::makeInstance(PackageResolverService::class);
            $availablePackages = $packageResolver->getAvailablePackages();
            $selectedPackage = $availablePackages[$parsedBody['packageName']];
            $selectedPackageName = $selectedPackage->getValueFromComposerManifest()->name;
            $explodedName = GeneralUtility::trimExplode('/', $selectedPackageName);
            $vendorName = isset($explodedName[0]) ? $explodedName[0] : '';

            if (!$this->isValid($vendorName)) {
                $error = 'Your vendor name does not match the requirement.';
                $validate = false;
            }
            if (!$this->isValid($parsedBody['contentBlockKey'])) {
                $error = 'Your Content Block Key does not match the requirements.';
                $validate = false;
            }
            if ($validate) {
                $contentBlockKey = $vendorName . '/' . $parsedBody['contentBlockKey'];
                $extension = $parsedBody['packageName'];
                $base = 'EXT:' . $extension . '/';
                $path = $base . ContentBlockPathUtility::getRelativeContentElementsPath();
                $basePath = GeneralUtility::getFileAbsFileName($path);
                try {
                    $this->validateKeyAndPath($basePath, $parsedBody['contentBlockKey'], $extension);
                    $validYamlArrayJson = json_decode($parsedBody['validYamlArrayJson'], true, 512, 1);
                    $finalizeFields = [];
                    if (!empty($validYamlArrayJson)) {
                        foreach ($validYamlArrayJson as $field) {
                            if (
                                isset($parsedBody[$field['identifier'].'-type']) &&
                                isset($parsedBody[$field['identifier'].'-label'])
                            ) {
                                $field['type'] = $parsedBody[$field['identifier'].'-type'];
                                $field['label'] = $parsedBody[$field['identifier'].'-label'];
                                if (isset($parsedBody[$field['identifier'].'-required']) && $parsedBody[$field['identifier'].'-required'] === 'on') {
                                    $field['required'] = true;
                                } else {
                                    $field['required'] = false;
                                }
                                $finalizeFields[] = $field;
                            }
                        }
                        if (!empty($parsedBody['new-type']) && !empty($parsedBody['new-label'])) {
                            foreach ($parsedBody['new-label'] as $key => $label) {
                                $identifier = StringUtility::getUniqueId('NEW');
                                $newLabel = $label;
                                $newType = $parsedBody['new-type'][$key] ?? 'Text';
                                $finalizeFields[] = [
                                    'identifier' => $identifier,
                                    'label' => $newLabel,
                                    'type' => $newType,
                                    'required' => isset($parsedBody['new-required'][$key]) ? $parsedBody['new-required'][$key] : false,
                                ];
                            }
                        }
                    }
                    $validYamlArray = [];
                    foreach ($finalizeFields as $field) {
                        $validYamlArray[] = ContentBlocksYamlConfig::getFieldConfig($field);
                    }
                    $yamlConfiguration = $this->createContentBlockContentElementConfiguration(
                        $validYamlArray,
                        $parsedBody['category'] ?? 't3ab_content_blocks',
                        $vendorName,
                        $parsedBody['contentBlockKey'],
                        $parsedBody['elementName'],
                        $parsedBody['elementDescription'],
                    );
                    if ($parsedBody['cbIcon'] !== '' && $this->typo3Version < 14) {
                        $icon = new ContentTypeIcon('', '', $parsedBody['cbIcon']);
                    } else {
                        $icon = new ContentTypeIcon();
                    }
                    if ($this->typo3Version === 12) {
                        $contentBlockConfiguration = new LoadedContentBlock(
                            name: $contentBlockKey,
                            yaml: $yamlConfiguration,
                            icon: $icon,
                            hostExtension: $extension,
                            extPath: $path,
                            contentType: ContentType::CONTENT_ELEMENT,
                            iconHideInMenu: $icon,
                        );
                    } else {
                        $contentBlockConfiguration = new LoadedContentBlock(
                            name: $contentBlockKey,
                            yaml: $yamlConfiguration,
                            icon: $icon,
                            hostExtension: $extension,
                            extPath: $path,
                            contentType: ContentType::CONTENT_ELEMENT
                        );
                    }
                    $this->contentBlockBuilder->create($contentBlockConfiguration);
                    $this->createFrontendHtml($parsedBody['contentBlockKey'], $basePath);
                    if ($this->typo3Version >= 13) {
                        $this->setIcon($parsedBody['contentBlockKey'], $basePath, $parsedBody['cbIcon']);
                    }
                    $files = $this->getContentBlockFiles($parsedBody['packageName'], $parsedBody['contentBlockKey']);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        } else {
            $error = $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':error_title');
        }
        if ($error === '') {
            $success = true;
            $this->clearCacheService->clearAll();
            GeneralUtility::makeInstance(OpcodeCacheService::class)->clearAllActive();
        }
        $assign = [
            'error' => $error,
            'files' => $files,
        ];
        if ($success) {
            $this->showFlashMessage(
                $this->getLanguageService()->sL($this::LANGUAGE_FILE. ':content_blocks.create.success'),
                '',
                ''
            );
        } else {
            $this->showFlashMessage(
                $this->getLanguageService()->sL($this::LANGUAGE_FILE. ':t3ab.error'),
                $error,
                'ERROR'
            );
        }
        return $this->getViewAndTemplate($request, $assign, 'ContentBlocksModule/', 'GeneratedContentBlockPreview');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function deleteContentBlockAction(ServerRequestInterface $request): ResponseInterface
    {
        $error = $backupStoreDir = '';
        $success = $isBackup = false;
        $parsedBody = $request->getParsedBody();
        if (
            isset($parsedBody['cbExtPath']) &&
            isset($parsedBody['cbName'])
        ) {
            $basePath = GeneralUtility::getFileAbsFileName($parsedBody['cbExtPath']);
            // $basePath .= '/' . $parsedBody['cbName'];
            if (!file_exists($basePath)) {
                $error = 'Content Block does not exist';
            } else {
                // Store Backup of Content Block
                $backupDir = 't3ab/content_blocks_backup/'.$parsedBody['cbName'];
                $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
                $storage = $resourceFactory->getDefaultStorage();
                $defaultStorageName = $storage->getConfiguration()['basePath'] ?? '';
                if (!$storage->hasFolder($backupDir)) {
                    $storage->createFolder($backupDir);
                } else {
                    $storage->getFolder($backupDir);
                }
                $backupStoreDir = $defaultStorageName.$backupDir;
                try {
                    // Copy to backup folder
                    GeneralUtility::copyDirectory($basePath, $backupStoreDir);
                    $isBackup = true;
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }

                // Delete Content Block
                $delete = GeneralUtility::rmdir($basePath, true);
                if ($delete) {
                    $success = true;
                } else {
                    $error = $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':error_title');
                }
                $this->clearCacheService->clearAll();
                GeneralUtility::makeInstance(OpcodeCacheService::class)->clearAllActive();
            }
        }
        return new JsonResponse([
            'success' => $success,
            'error' => $error,
            'isBackup' => $isBackup,
            'backupDir' => $backupStoreDir,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function keySuggestAction(ServerRequestInterface $request): ResponseInterface
    {
        $providedKey = $request->getParsedBody()['providedKey'];
        $key = preg_replace('/[^A-Za-z0-9 -]/', '', $this->normalize($providedKey, '-'));
        $extension = $request->getParsedBody()['packageName'];
        $base = 'EXT:' . $extension . '/';
        $path = $base . ContentBlockPathUtility::getRelativeContentElementsPath();
        $basePath = GeneralUtility::getFileAbsFileName($path);
        $uniqueKey = $key;
        if ($this->checkContentBlockExists($basePath, $key)) {
            $counter = 0;
            while (
                $this->checkContentBlockExists($basePath, $uniqueKey) &&
                $this->isValid($uniqueKey)
                && ++$counter <= 100
            ) {
                $uniqueKey = $this->normalize($key . '-' . $counter);
            }
            if ($counter === 100) {
                $uniqueId = StringUtility::getUniqueId();
                $uniqueKey = $this->normalize($key . '-' . md5($uniqueId));
            }
        }
        return new JsonResponse([
            'validateKey' => $uniqueKey,
        ]);
    }

    public function filesPreviewAction(ServerRequestInterface $request): ResponseInterface
    {
        $requestParams = $request->getQueryParams();
        $error = '';
        $files = [];

        if (!empty($requestParams['contentBlockKey']) && !empty($requestParams['packageName'])) {
            $files = $this->getContentBlockFiles($requestParams['packageName'], $requestParams['contentBlockKey']);
        } else {
            $error = $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':error_title');
        }
        $assign = [
            'error' => $error,
            'files' => $files,
        ];
        return $this->getViewAndTemplate($request, $assign, 'ContentBlocksModule/', 'GeneratedContentBlockPreview');
    }

    /**
     * Normalizes a value to be used directly as path segment of a URL.
     */
    private function normalize(string $value, ?string $fallbackCharacter = '-'): string
    {
        $fallbackCharacter ??= '-';
        // Convert to lowercase + remove tags
        $value = mb_strtolower($value, 'utf-8');
        $value = strip_tags($value);

        // Convert some special tokens (space, "_" and "-") to the space character
        $value = (string)preg_replace('/[ \t\x{00A0}\-+_]+/u', $fallbackCharacter, $value);

        if (!Normalizer::isNormalized($value)) {
            $value = \Normalizer::normalize($value) ?: $value;
        }

        // Convert extended letters to ascii equivalents
        // v14 removed specCharsToASCII(); utf8_char_mapping() is the replacement
        $charsetConverter = GeneralUtility::makeInstance(CharsetConverter::class);
        $value = $charsetConverter->utf8_char_mapping($value);

        // Get rid of all invalid characters, but allow slashes
        $value = (string)preg_replace('/[^\p{L}\p{M}0-9\/' . preg_quote($fallbackCharacter) . ']/u', '', $value);

        // Convert multiple fallback characters to a single one
        if ($fallbackCharacter !== '') {
            $value = (string)preg_replace('/' . preg_quote($fallbackCharacter) . '{2,}/', $fallbackCharacter, $value);
        }

        // Ensure slug is lower cased after all replacement was done
        $value = mb_strtolower($value, 'utf-8');
        // Extract slug, thus it does not have wrapping fallback and slash characters
        $extractedSlug = trim($value, $fallbackCharacter . '/');

        // Remove trailing and beginning slashes, except if the trailing slash was added, then we'll re-add it
        $appendTrailingSlash = $extractedSlug !== '' && substr($value, -1) === '/';
        $value = $extractedSlug . ($appendTrailingSlash ? '/' : '');

        return $value;
    }

    /**
     * @param string $basePath
     * @return boolean
     */
    private function checkContentBlockExists(string $basePath, string $name): bool
    {
        $basePath .= '/' . $name;
        return file_exists($basePath);
    }

    /**
     * @param string $name
     * @return bool
     */
    private static function isValid(string $name): bool
    {
        return (bool)preg_match('/^[a-z0-9]((-{1,2})?[a-z0-9]+)*$/', $name);
    }

    /**
     * @param string $basePath
     * @param string $name
     * @param string $packageKey
     */
    private function validateKeyAndPath(string $basePath, string $name, string $packageKey): void
    {
        if ($basePath === '') {
            throw new \RuntimeException('Path to package "' . $name . '" cannot be empty.', 1674225339);
        }
        if ($this->checkContentBlockExists($basePath, $name)) {
            throw new \RuntimeException(
                'A Content Block with the folder name "' . $name . '" already exists in extension "' . $packageKey . '".',
                1674225340
            );
        }
    }

    /**
     * @param array $fieldArray
     * @param string $category
     * @param string $vendor
     * @param string $name
     * @param string $title
     * @param string|null $typeName
     * @return array
     */
    private function createContentBlockContentElementConfiguration(
        array $fieldArray,
        string $category,
        string $vendor,
        string $name,
        string $title,
        string $description = ''
    ): array {
        $fullName = $vendor . '/' . $name;
        $configuration = [
            't3ab' => 1,
            'table' => 'tt_content',
            'typeField' => 'CType',
            'name' => $fullName,
            'typeName' => UniqueIdentifierCreator::createContentTypeIdentifier($fullName),
            'title' => $title,
            'description' => $description,
            't3abTitle' => $title,
            't3abDescription' => $description,
            'group' => $category,
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
        foreach ($fieldArray as $field) {
            $configuration['fields'][] = $field;
        }
        return $configuration;
    }

    /**
     * @param string $contentBlockName
     * @param string $basePath
     * @return void
     */
    protected function createFrontendHtml(string $contentBlockName, string $basePath): void
    {
        GeneralUtility::writeFile(
            $basePath . '/' . $contentBlockName . '/' . ContentBlockPathUtility::getFrontendTemplatePath(),
            '<f:debug>{data._processed}</f:debug>'
        );
    }

    /**
     * @param string $contentBlockName
     * @param string $basePath
     * @param string $iconIdentifier
     * @return void
     */
    protected function setIcon(string $contentBlockName, string $basePath, string $iconIdentifier): void
    {
        if ($iconIdentifier !== '') {
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
            $iconSvg = $iconFactory->getIcon($iconIdentifier)->getMarkup('inline');
            GeneralUtility::writeFile(
                $basePath . '/' . $contentBlockName . '/' . ContentBlockPathUtility::getAssetsFolder() . '/icon.svg',
                $iconSvg
            );
        }
    }

    /**
     * @return array
     */
    private function getAvailableContentBlocks(): array
    {
        $list = [];
        foreach ($this->contentBlockRegistry->getAll() as $loadedContentBlock) {
            $table = match ($loadedContentBlock->getContentType()) {
                ContentType::CONTENT_ELEMENT, ContentType::PAGE_TYPE => $loadedContentBlock->getContentType()->getTable(),
                default => null,
            };
            if ($table === null) {
                continue;
            }
            if (isset($loadedContentBlock->getYaml()['t3ab']) && $loadedContentBlock->getYaml()['t3ab'] === 1) {
                $typeName = $loadedContentBlock->getYaml()['typeName'];
                $list[] = [
                    'vendor' => $loadedContentBlock->getVendor(),
                    'name' => $loadedContentBlock->getPackage(),
                    'extPath' => $loadedContentBlock->getExtPath(),
                    'table' => $table,
                    'type-name' => $typeName,
                    'title' => $loadedContentBlock->getYaml()['t3abTitle'] ?? '',
                    'description' => $loadedContentBlock->getYaml()['t3abDescription'] ?? '',
                    'content-type' => $loadedContentBlock->getContentType()->getHumanReadable(),
                    'extension' => $loadedContentBlock->getHostExtension(),
                    // @extensionScannerIgnoreLine
                    'iconData' => $loadedContentBlock->getIcon()->toArray(),
                ];
            }
        }
        return $list;
    }

    /**
     * @param string $packageName
     * @param string $contentBlockKey
     * @return array
     */
    private function getContentBlockFiles(string $packageName, string $contentBlockKey): array
    {
        $base = 'EXT:' . $packageName . '/';
        $relativePath = $base . ContentBlockPathUtility::getRelativeContentElementsPath();
        $basePath = GeneralUtility::getFileAbsFileName($relativePath);
        $mainPath = $basePath . '/' . $contentBlockKey . '/';

        // Absolute paths to files
        $frontendTemplatePath = $mainPath . ContentBlockPathUtility::getFrontendTemplatePath();
        $backendPreviewTemplatePath = $mainPath . ContentBlockPathUtility::getBackendPreviewPath();
        $yamlPath = $mainPath . ContentBlockPathUtility::getContentBlockDefinitionFileName();
        $languageFilePath = $mainPath . ContentBlockPathUtility::getLanguageFilePath();

        // Relative paths (as keys)
        $yamlKey = $relativePath . ContentBlockPathUtility::getContentBlockDefinitionFileName();
        $frontendKey = $relativePath . ContentBlockPathUtility::getFrontendTemplatePath();
        $backendKey = $relativePath . ContentBlockPathUtility::getBackendPreviewPath();
        $langKey = $relativePath . ContentBlockPathUtility::getLanguageFilePath();

        // Helper for loading content safely
        $getFileContents = function (string $path): string {
            return file_exists($path) ? file_get_contents($path) : '';
        };

        return [
            $yamlKey => $getFileContents($yamlPath),
            $frontendKey => $getFileContents($frontendTemplatePath),
            $backendKey => $getFileContents($backendPreviewTemplatePath),
            $langKey => $getFileContents($languageFilePath),
        ];
    }
}

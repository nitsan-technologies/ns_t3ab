<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Controller;

use GuzzleHttp\Exception\ClientException;
use NITSAN\NsT3Ab\Service\Form\FormPersistenceBridgeFactory;
use NITSAN\NsT3Ab\Service\Form\FormPersistenceBridgeInterface;
use NITSAN\NsT3Ab\Utility\HelperUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Form\Type\FormDefinitionArray;

class FormsModuleController extends AbstractController
{
    protected array $formSettings = [];

    protected FormPersistenceBridgeInterface $formPersistence;

    public function __construct()
    {
        parent::__construct();
        $this->formPersistence = GeneralUtility::makeInstance(FormPersistenceBridgeFactory::class)->get();
        $this->formSettings = $this->formPersistence->getFormSettings();
    }

    /**
     * @return ResponseInterface
     */
    public function renderFormGenerationTemplateAction(ServerRequestInterface $request): ResponseInterface
    {
        $extConf = HelperUtility::getExtensionGlobalSettings();
        $assign = [
            'defaultModel' => isset($extConf['defaultModel']) ? $extConf['defaultModel'] : null,
            'extensionConfig' => $extConf,
        ];
        $this->pageRenderer->loadJavaScriptModule('@nitsan/nst3ab/forms-module.js');
        return $this->getViewAndTemplate($request, $assign, 'FormsModule/', 'FormGeneration');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function generateFormFieldsAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $aiModel = !empty($parsedBody['text-api-model']) ? $parsedBody['text-api-model'] : 'openai';
        $aiSelectedModel = HelperUtility::getSelectedModal($parsedBody);
        $parsedBody['text-api-model'] = $aiModel;
        if (!isset($parsedBody['aiSelectedModel'])) {
            $parsedBody['aiSelectedModel'] = $aiSelectedModel;
        } else {
            $aiSelectedModel = $parsedBody['aiSelectedModel'];
        }
        $prompt = $parsedBody['prompt'];
        $errorTitle = $this->getLanguageService()->sL($this::LANGUAGE_FILE. ':error_title');
        $redirectUrl = $this->backendUriBuilder->buildUriFromRoute('ai_form');
        try {
            $aiResult = $this->aiService->requestAi(
                $prompt,
                'AiForm',
                $parsedBody
            );
            $resultData = HelperUtility::parseAiJsonResponse($aiResult);
            if ($resultData === null) {
                throw new \RuntimeException(
                    $this->getLanguageService()->sL($this::LANGUAGE_FILE . ':general_error_message') ?: 'Invalid AI response'
                );
            }

            $templatePath = 'EXT:form/Resources/Private/Backend/Templates/FormEditor/Yaml/NewForms/BlankForm.yaml';
            $templateFormPath = GeneralUtility::getFileAbsFileName($templatePath);
            $form = Yaml::parse((string)file_get_contents($templateFormPath));
            $savePath = $this->formPersistence->getSavePath();
            if (isset($resultData['form'])) {
                $resultData = $resultData['form'];
            }
            $form['label'] = $resultData['title'] ?? 'AI Form';
            $form['identifier'] = uniqid('t3ab_');
            $form['prototypeName'] = 'standard';

            $formPersistenceIdentifier = $this->formPersistence->getUniquePersistenceIdentifier($form['identifier'], $savePath);
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeFormCreate'] ?? [] as $className) {
                $hookObj = GeneralUtility::makeInstance($className);
                if (method_exists($hookObj, 'beforeFormCreate')) {
                    $form = $hookObj->beforeFormCreate(
                        $formPersistenceIdentifier,
                        $form
                    );
                }
            }
            if (isset($resultData['fields'])) {
                $form['renderables'][0]['renderables'] = $resultData['fields'] ?? [];
            } else {
                $form['renderables'][0]['renderables'] = $resultData['elements'] ?? [];
            }

            $updatedRenderables = [];
            foreach ($form['renderables'][0]['renderables'] as $item) {
                $item['type'] = ucwords(strtolower($item['type']));

                // Replace Identifier as per the TYPO3 Default Form
                $typeMappings = [
                    'Input' => 'Text',
                    'Select' => 'SingleSelect',
                    'File' => 'FileUpload',
                    'Fileupload' => 'FileUpload',
                ];
                $item['type'] = $typeMappings[$item['type']] ?? $item['type'];

                // Remove Submit Button
                if ($item['type'] === 'Submit' || $item['type'] === 'Button') {
                    continue;
                }
                $updatedRenderables[] = $item;

            }
            $form['renderables'][0]['renderables'] = $updatedRenderables;

            // Replace the key as per the TYPO3 Default Form
            $updatedRenderablesChild = [];
            foreach ($form['renderables'][0]['renderables'] as $childItem) {
                $changeKey = [];
                foreach ($childItem as $key => $value) {
                    $newKey = ($key === 'name') ? 'identifier' : $key;
                    $changeKey[$newKey] = $value;
                }
                $updatedRenderablesChild[] = $changeKey;
            }

            $form['renderables'][0]['renderables'] = $updatedRenderablesChild;

            $fileToSave = ArrayUtility::stripTagsFromValuesRecursive($form);
            $formDefinitionArray = new FormDefinitionArray($fileToSave);

            try {
                $formDefinitionArray = $formDefinitionArray->getArrayCopy();
                foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeFormSave'] ?? [] as $className) {
                    $hookObj = GeneralUtility::makeInstance($className);
                    if (method_exists($hookObj, 'beforeFormSave')) {
                        $formDefinitionArray = $hookObj->beforeFormSave(
                            $formPersistenceIdentifier,
                            $formDefinitionArray
                        );
                    }
                }

            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                $this->showFlashMessage(
                    $errorTitle,
                    $errorMsg,
                    'ERROR'
                );
                return new RedirectResponse($redirectUrl);
            }

            $formElements = $this->formSettings['prototypes']['standard']['formElementsDefinition'] ?? [];

            $latestFormElements = array_filter($formElements, function ($key) {
                return !in_array(
                    $key,
                    ['Form', 'Page', 'SummaryPage', 'Fieldset', 'GridRow', 'Honeypot', 'StaticText', 'ContentElement']
                );
            }, ARRAY_FILTER_USE_KEY);

            $assign = [
                'prompt' => $prompt,
                'text-api-model' => $aiModel,
                'aiSelectedModel' => $aiSelectedModel,
                'formData' => $aiResult,
                'responseForm' => $formDefinitionArray,
                'formStructure' => json_encode($formDefinitionArray),
                'formElements' => $latestFormElements,
            ];
            $this->pageRenderer->loadJavaScriptModule('@nitsan/nst3ab/forms-module.js');
            return $this->getViewAndTemplate($request, $assign, 'FormsModule/', 'GeneratedFormFields');
        } catch (ClientException $e) {
            $errorMsg = $this->guzzleClientExceptionHandler($e);
            if ($errorMsg === '') {
                $errorMsg =  $this->getLanguageService()->sL($this::LANGUAGE_FILE. ':general_error_message');
            }
            $this->showFlashMessage(
                $errorTitle,
                $errorMsg,
                'ERROR'
            );
            return new RedirectResponse($redirectUrl);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            $this->showFlashMessage(
                $errorTitle,
                $errorMsg,
                'ERROR'
            );
            return new RedirectResponse($redirectUrl);
        }
        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function createAiFormAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $selectedTypo3FormIdentifier = $parsedBody['selectedTypo3FormIdentifier'] ?? '';
        $typo3FormResponseData = json_decode($parsedBody['typo3FormResponseData'] ?? '', true);
        $savePath = $this->formPersistence->getSavePath();
        $finalizeFields = $finalizeFormData = [];

        if (!empty($parsedBody['new-type']) && !empty($parsedBody['new-label'])) {
            foreach ($parsedBody['new-label'] as $key => $label) {
                if (isset($parsedBody['new-identifier'][$key])) {
                    $identifier = $parsedBody['new-identifier'][$key];
                } else {
                    $identifier = StringUtility::getUniqueId('NEW');
                }
                $newLabel = $label;
                $newType = $parsedBody['new-type'][$key] ?? 'Text';
                $finalizeFields[] = [
                    'identifier' => $identifier,
                    'label' => $newLabel,
                    'type' => $newType,
                ];
            }
            $finalizeFormData = $typo3FormResponseData;
            $finalizeFormData['renderables'][0] = [
                'type' => 'Page',
                'identifier' => 'page-1',
                'label' => 'Step',
                'renderables' => $finalizeFields,
            ];
        } else {
            $finalizeFormData = $typo3FormResponseData;
        }

        try {
            $formPersistenceIdentifier = $this->formPersistence->getUniquePersistenceIdentifier(
                $selectedTypo3FormIdentifier,
                $savePath
            );
            $fileToSave = ArrayUtility::stripTagsFromValuesRecursive($finalizeFormData);
            $formDefinitionArray = new FormDefinitionArray($fileToSave);
            $formDefinitionArray = $formDefinitionArray->getArrayCopy();
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeFormSave'] ?? [] as $className) {
                $hookObj = GeneralUtility::makeInstance($className);
                if (method_exists($hookObj, 'beforeFormSave')) {
                    $formDefinitionArray = $hookObj->beforeFormSave(
                        $formPersistenceIdentifier,
                        $formDefinitionArray
                    );
                }
            }

            if ($this->typo3Version >= 14) {
                $generateFormUrl = (string)$this->backendUriBuilder->buildUriFromRoute('form_editor', [
                    'formPersistenceIdentifier' => $formPersistenceIdentifier,
                ]);
            } else {
                $formModuleUrl = $this->backendUriBuilder->buildUriFromRoute('web_FormFormbuilder');
                $generateFormUrl = $formModuleUrl->getPath() . '/FormEditor/index?formPersistenceIdentifier=' . $formPersistenceIdentifier;
            }
            $this->formPersistence->save($formPersistenceIdentifier, $formDefinitionArray, $savePath);
            return new JsonResponse(['redirectUrl' => $generateFormUrl]);
        } catch (\TYPO3\CMS\Core\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }
    }
}

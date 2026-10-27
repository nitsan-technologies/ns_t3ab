<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Controller;

use GuzzleHttp\Exception\ClientException;
use NITSAN\NsT3Ab\Service\AiService;
use NITSAN\NsT3Ab\Service\FlashMessageService;
use NITSAN\NsT3Ab\Utility\HelperUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService as CoreFlashMessageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

abstract class AbstractController extends ActionController
{
    protected ModuleTemplateFactory $moduleTemplateFactory;

    protected int $typo3Version;

    protected PageRenderer $pageRenderer;

    protected AiService $aiService;

    protected FlashMessageService $flashMessageService;

    protected UriBuilder $backendUriBuilder;

    protected const LANGUAGE_FILE = 'LLL:EXT:ns_t3ab/Resources/Private/Language/locallang_be.xlf';

    public function __construct()
    {
        $this->moduleTemplateFactory = GeneralUtility::makeInstance(ModuleTemplateFactory::class);
        $this->typo3Version = HelperUtility::getCurrentTypo3Version();
        $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $this->aiService = GeneralUtility::makeInstance(AiService::class);
        $this->flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $this->backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this->pageRenderer->addInlineLanguageLabelFile('EXT:ns_t3ab/Resources/Private/Language/locallang_be.xlf');
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $assign
     * @param string $dir
     * @param string $template
     * @return ResponseInterface
     */
    protected function getViewAndTemplate(
        ServerRequestInterface $request,
        array $assign,
        string $dir,
        string $template
    ): ResponseInterface {
        $view = $this->initializeModuleTemplate($request);
        $view->assignMultiple($assign);
        return $view->renderResponse($dir . $template);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ModuleTemplate
     */
    protected function initializeModuleTemplate(ServerRequestInterface $request): ModuleTemplate
    {
        return $this->moduleTemplateFactory->create($request);
    }

    /**
     * @param ClientException $e
     * @return string
     */
    protected function guzzleClientExceptionHandler(ClientException $e): string
    {
        $responseBody = $e->getResponse()->getBody()->getContents();
        $error = json_decode($responseBody, true);
        if (isset($error['error']['message'])) {
            return $error['error']['message'];
        }
        return '';
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @param string $label
     * @param string $messages
     * @param string $type
     */
    protected function showFlashMessage(string $label, string $messages, string $type): void
    {
        $flashMessageService = GeneralUtility::makeInstance(CoreFlashMessageService::class);
        $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier(FlashMessageQueue::NOTIFICATION_QUEUE);
        $severity = HelperUtility::getSeverityClass($type);
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $messages,
            $label,
            $severity,
            true
        );
        $flashMessageQueue->enqueue($flashMessage);
    }
}

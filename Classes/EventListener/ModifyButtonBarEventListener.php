<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\EventListener;

use NITSAN\NsT3Ab\Utility\HelperUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Module\ModuleData;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;

final class ModifyButtonBarEventListener
{
    private const FORM_MANAGER_MODULES = ['web_FormFormbuilder', 'form_manager'];

    private const FORM_MODULE_IDENTIFIERS = ['web_FormFormbuilder', 'form_manager', 'form_editor'];

    public function __invoke(ModifyButtonBarEvent $event): void
    {
        $request = $this->resolveRequest($event);

        if (!$this->isFormModuleRequest($request)) {
            return;
        }

        $buttons = $event->getButtons();
        if (isset($buttons[ButtonBar::BUTTON_POSITION_LEFT])) {
            foreach ($buttons[ButtonBar::BUTTON_POSITION_LEFT] as &$buttonGroup) {
                foreach ($buttonGroup as &$button) {
                    if ($button->getTitle() === 'Close') {
                        $button->setHref($this->getReturnUrl());
                    }
                }
            }
        }

        if (!$this->isFormManagerRequest($request)) {
            $event->setButtons($buttons);
            return;
        }

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $typo3Version = HelperUtility::getCurrentTypo3Version();
        if ($typo3Version === 12) {
            // @extensionScannerIgnoreLine
            $icon = $iconFactory->getIcon('t3ab_magic', \TYPO3\CMS\Core\Imaging\Icon::SIZE_SMALL);
        } else {
            $icon = $iconFactory->getIcon('t3ab_magic', IconSize::SMALL);
        }

        $label = $this->getLanguageService()->sL('LLL:EXT:ns_t3ab/Resources/Private/Language/locallang_be.xlf:module.forms.generate_ai_form');
        $linkButton = $this->createLinkButton($event)
            ->setHref('#')
            ->setClasses('btn-t3ab-main ms-2 create-ai-form')
            ->setTitle($label)
            ->setShowLabelText(true)
            ->setIcon($icon);

        $buttons[ButtonBar::BUTTON_POSITION_LEFT][50][] = $linkButton;
        $event->setButtons($buttons);

        GeneralUtility::makeInstance(PageRenderer::class)->loadJavaScriptModule('@nitsan/nst3ab/modal-view.js');
    }

    private function isFormModuleRequest(ServerRequestInterface $request): bool
    {
        $extbase = $request->getAttribute('extbase');
        if ($extbase instanceof ExtbaseRequestParameters) {
            return in_array($extbase->getControllerName(), ['FormManager', 'FormEditor'], true);
        }

        $moduleData = $request->getAttribute('moduleData');
        if ($moduleData instanceof ModuleData) {
            return in_array($moduleData->getModuleIdentifier(), self::FORM_MODULE_IDENTIFIERS, true);
        }

        return false;
    }

    private function isFormManagerRequest(ServerRequestInterface $request): bool
    {
        $extbase = $request->getAttribute('extbase');
        if ($extbase instanceof ExtbaseRequestParameters) {
            return $extbase->getControllerName() === 'FormManager';
        }

        $moduleData = $request->getAttribute('moduleData');
        if ($moduleData instanceof ModuleData) {
            return in_array($moduleData->getModuleIdentifier(), self::FORM_MANAGER_MODULES, true);
        }

        return false;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * getRequest() exists only on TYPO3 14+; v12/v13 use $GLOBALS['TYPO3_REQUEST'].
     */
    private function resolveRequest(ModifyButtonBarEvent $event): ServerRequestInterface
    {
        if (method_exists($event, 'getRequest')) {
            return $event->getRequest();
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            throw new \RuntimeException('Backend request is not available for ModifyButtonBarEvent.', 1754817480);
        }

        return $request;
    }

    /**
     * ComponentFactory is TYPO3 14+; ButtonBar::makeLinkButton() works on v12/v13 (deprecated in v14).
     */
    private function createLinkButton(ModifyButtonBarEvent $event): LinkButton
    {
        $factoryClass = 'TYPO3\\CMS\\Backend\\Template\\Components\\ComponentFactory';
        if (class_exists($factoryClass)) {
            return GeneralUtility::makeInstance($factoryClass)->createLinkButton();
        }

        // @extensionScannerIgnoreLine
        return $event->getButtonBar()->makeLinkButton();
    }

    private function getReturnUrl(): string
    {
        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $route = HelperUtility::getCurrentTypo3Version() >= 14 ? 'form_manager' : 'web_FormFormbuilder';

        return $backendUriBuilder->buildUriFromRoute($route)->__toString();
    }
}

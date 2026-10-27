<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Service;

use NITSAN\NsT3Ab\Utility\HelperUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService as FlashService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlashMessageService
{
    protected FlashService $flashMessageService;

    public function __construct()
    {
        $this->flashMessageService = GeneralUtility::makeInstance(FlashService::class);
    }

    /**
     * @param string $message
     * @param string $title
     * @return void
     */
    public function addSuccessMessage(string $message, string $title = ''): void
    {
        $this->addFlashMessageWithSeverity(
            $title,
            $message,
            HelperUtility::getSeverityClass('OK'),
        );
    }

    /**
     * @param string $message
     * @param string $title
     * @return void
     */
    public function addErrorMessage(string $message, string $title = ''): void
    {
        $this->addFlashMessageWithSeverity(
            $title,
            $message,
            HelperUtility::getSeverityClass('ERROR')
        );
    }

    /**
     * @param string $message
     * @param string $title
     * @return void
     */
    public function addWarningMessage(string $message, string $title = ''): void
    {
        $this->addFlashMessageWithSeverity(
            $title,
            $message,
            HelperUtility::getSeverityClass('WARNING')
        );
    }

    /**
     * @param string $title
     * @param string $message
     * @param mixed $severity
     * @return void
     */
    protected function addFlashMessageWithSeverity(
        string $title,
        string $message,
        $severity
    ): void {
        $query = $this->flashMessageService->getMessageQueueByIdentifier('ns_t3ab');

        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $message,
            $title,
            $severity,
            true
        );
        // @extensionScannerIgnoreLine
        $query->addMessage($flashMessage);
    }
}

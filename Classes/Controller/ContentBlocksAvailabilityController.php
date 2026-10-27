<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Controller;

class ContentBlocksAvailabilityController extends AbstractController
{
    public function contentBlocksNotInstalledAction()
    {
        $this->flashMessageService->addWarningMessage(
            $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':content_blocks.extension_not_installer.description'),
            $this->getLanguageService()->sL(self::LANGUAGE_FILE. ':content_blocks.extension_not_installer.title'),
        );
        return $this->getViewAndTemplate($this->request, [], 'ContentBlocksModule/', 'ContentBlocksNotInstalled');
    }
}

<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Service\Form;

use NITSAN\NsT3Ab\Utility\HelperUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface as ExtbaseConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManagerInterface as FormConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface;

/**
 * TYPO3 v12 / v13 EXT:form persistence API.
 */
final class FormPersistenceBridgeLegacy implements FormPersistenceBridgeInterface
{
    public function __construct(
        private readonly FormPersistenceManagerInterface $formPersistenceManager,
        private readonly FormConfigurationManagerInterface $formConfigurationManager,
        private readonly ExtbaseConfigurationManagerInterface $configurationManager,
    ) {
    }

    public function getFormSettings(): array
    {
        if (HelperUtility::getCurrentTypo3Version() >= 13) {
            $typoScriptSettings = $this->configurationManager->getConfiguration(
                ExtbaseConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'form'
            );

            return $this->formConfigurationManager->getYamlConfiguration($typoScriptSettings, false);
        }

        return $this->formConfigurationManager->getConfiguration(
            FormConfigurationManagerInterface::CONFIGURATION_TYPE_YAML_SETTINGS,
            'form'
        );
    }

    public function getSavePath(): string
    {
        $allowedFileMounts = $this->getFormSettings()['persistenceManager']['allowedFileMounts'] ?? [];
        if ($allowedFileMounts === []) {
            throw new \RuntimeException('No allowed form file mounts configured.', 1722259201);
        }

        return (string)end($allowedFileMounts);
    }

    public function getUniquePersistenceIdentifier(string $formIdentifier, string $savePath): string
    {
        if (HelperUtility::getCurrentTypo3Version() >= 13) {
            return $this->formPersistenceManager->getUniquePersistenceIdentifier(
                $formIdentifier,
                $savePath,
                $this->getFormSettings()
            );
        }

        return $this->formPersistenceManager->getUniquePersistenceIdentifier($formIdentifier, $savePath);
    }

    public function save(string $persistenceIdentifier, array $formDefinition, string $savePath): void
    {
        if (HelperUtility::getCurrentTypo3Version() >= 13) {
            $this->formPersistenceManager->save(
                $persistenceIdentifier,
                $formDefinition,
                $this->getFormSettings()
            );
            return;
        }

        $this->formPersistenceManager->save($persistenceIdentifier, $formDefinition);
    }
}

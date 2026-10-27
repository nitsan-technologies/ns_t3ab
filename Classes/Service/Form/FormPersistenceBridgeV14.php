<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Service\Form;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface as ExtbaseConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManagerInterface as FormConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface;

/**
 * TYPO3 v14 EXT:form persistence API (storage adapters / filemount).
 */
final class FormPersistenceBridgeV14 implements FormPersistenceBridgeInterface
{
    public function __construct(
        private readonly FormPersistenceManagerInterface $formPersistenceManager,
        private readonly FormConfigurationManagerInterface $formConfigurationManager,
        private readonly ExtbaseConfigurationManagerInterface $configurationManager,
    ) {}

    public function getFormSettings(): array
    {
        $typoScriptSettings = $this->configurationManager->getConfiguration(
            ExtbaseConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'form'
        );

        return $this->formConfigurationManager->getYamlConfiguration($typoScriptSettings, false);
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
        return $this->formPersistenceManager->getUniquePersistenceIdentifier('filemount', $formIdentifier, $savePath);
    }

    public function save(string $persistenceIdentifier, array $formDefinition, string $savePath): void
    {
        $this->formPersistenceManager->save(
            $persistenceIdentifier,
            $formDefinition,
            $this->getFormSettings(),
            $savePath
        );
    }
}

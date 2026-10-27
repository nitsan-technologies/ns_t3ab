<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Service\Form;

/**
 * Version-specific EXT:form persistence helpers for AI Forms.
 */
interface FormPersistenceBridgeInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getFormSettings(): array;

    public function getSavePath(): string;

    public function getUniquePersistenceIdentifier(string $formIdentifier, string $savePath): string;

    /**
     * @param array<string, mixed> $formDefinition
     */
    public function save(string $persistenceIdentifier, array $formDefinition, string $savePath): void;
}

<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Service\Form;

use NITSAN\NsT3Ab\Utility\HelperUtility;

/**
 * Resolves the version-specific form persistence bridge.
 *
 * Must be a public DI service so controllers instantiated via
 * GeneralUtility::makeInstance() can resolve it from the container.
 */
final class FormPersistenceBridgeFactory
{
    public function __construct(
        private readonly FormPersistenceBridgeV14 $v14,
        private readonly FormPersistenceBridgeLegacy $legacy,
    ) {}

    public function get(): FormPersistenceBridgeInterface
    {
        return HelperUtility::getCurrentTypo3Version() >= 14 ? $this->v14 : $this->legacy;
    }
}

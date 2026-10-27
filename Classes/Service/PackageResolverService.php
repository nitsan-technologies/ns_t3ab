<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Service;

use Composer\InstalledVersions;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Package\PackageInterface;

class PackageResolverService
{
    /**
     * @return array<string, PackageInterface>
     */
    public function getAvailablePackages(): array
    {
        // @extensionScannerIgnoreLine
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $packages = $packageManager->getAvailablePackages();
        $packages = $this->removeFrameworkExtensions($packages);
        if (Environment::isComposerMode()) {
            return $this->filterNonLocalComposerPackages($packages);
        }
        return $packages;
    }

    public function getComposerProjectVendor(): string
    {
        if (!Environment::isComposerMode()) {
            return '';
        }
        $rootPackageName = $this->getRootPackageName();
        $parts = explode('/', $rootPackageName);
        $vendor = $parts[0];
        return $vendor;
    }

    /**
     * @param array<string, PackageInterface> $packages
     * @return array<string, PackageInterface>
     */
    protected function removeFrameworkExtensions(array $packages): array
    {
        return array_filter($packages, fn(PackageInterface $package): bool => !$package->getPackageMetaData()->isFrameworkType());
    }

    /**
     * @param array<string, PackageInterface> $packages
     * @return array<string, PackageInterface>
     */
    protected function filterNonLocalComposerPackages(array $packages): array
    {
        $composerLockPath = Environment::getProjectPath() . '/composer.lock';
        if (!file_exists($composerLockPath)) {
            return $packages;
        }
        $composerLock = json_decode(file_get_contents($composerLockPath), true);
        $composerLockPackages = array_merge($composerLock['packages'] ?? [], $composerLock['packages-dev'] ?? []);
        $composerLockMap = [];
        foreach ($composerLockPackages as $package) {
            $composerLockMap[$package['name']] = $package['dist']['type'] ?? null;
        }
        $filterPackages = function (PackageInterface $package) use ($composerLockMap): bool {
            $name = $package->getValueFromComposerManifest('name');
            if (array_key_exists($name, $composerLockMap)) {
                return $composerLockMap[$name] === 'path';
            }
            return $name === $this->getRootPackageName();
        };
        return array_filter($packages, $filterPackages);
    }

    /**
     * @return string
     */
    protected function getRootPackageName(): string
    {
        $rootPackageName = InstalledVersions::getRootPackage()['name'];
        return $rootPackageName;
    }
}
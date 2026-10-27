<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ExtensionMetadataTest extends TestCase
{
    public function testComposerDeclaresTheExtensionKeyAndSupportedCore(): void
    {
        $composer = json_decode((string)file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('nitsan/ns-t3ab', $composer['name']);
        self::assertSame('ns_t3ab', $composer['extra']['typo3/cms']['extension-key']);
        self::assertSame('^12.4 || ^13.4 || ^14.3', $composer['require']['typo3/cms-core']);
    }
}

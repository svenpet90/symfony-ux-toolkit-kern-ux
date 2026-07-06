<?php

declare(strict_types=1);

namespace KernUx\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Validates the recipe manifest.json shipped with every component, plus the
 * kit-level manifest.json at the repository root.
 */
final class ManifestTest extends TestCase
{
    private const ROOT = __DIR__.'/..';

    public function testRootManifestIsValid(): void
    {
        $data = $this->decode(self::ROOT.'/manifest.json');

        self::assertArrayHasKey('$schema', $data);
        self::assertArrayHasKey('name', $data);
        self::assertArrayHasKey('description', $data);
    }

    #[DataProvider('componentManifests')]
    public function testComponentManifestIsValid(string $file): void
    {
        $data = $this->decode($file);

        self::assertArrayHasKey('$schema', $data, 'Missing $schema');
        self::assertSame('component', $data['type'] ?? null, 'type must be "component"');
        self::assertNotEmpty($data['name'] ?? null, 'name must be set');
        self::assertNotEmpty($data['description'] ?? null, 'description must be set');
        self::assertArrayHasKey('copy-files', $data, 'copy-files must be set');
        self::assertSame(['templates/' => 'templates/'], $data['copy-files'], 'copy-files must copy templates/');
    }

    public function testEveryComponentFolderHasAManifest(): void
    {
        foreach (glob(self::ROOT.'/*/templates/components', \GLOB_ONLYDIR) as $componentsDir) {
            $componentDir = \dirname($componentsDir, 2);
            self::assertFileExists($componentDir.'/manifest.json', \sprintf('%s is missing a manifest.json', basename($componentDir)));
        }
    }

    /**
     * @return array<string, array{string}>
     */
    public static function componentManifests(): array
    {
        $cases = [];
        foreach (glob(self::ROOT.'/*/manifest.json') as $file) {
            $cases[basename(\dirname($file))] = [$file];
        }

        return $cases;
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string $file): array
    {
        self::assertFileExists($file);
        $data = json_decode((string) file_get_contents($file), true);
        self::assertIsArray($data, \sprintf('%s is not valid JSON', $file));

        return $data;
    }
}

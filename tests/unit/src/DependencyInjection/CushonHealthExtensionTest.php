<?php

declare(strict_types=1);

namespace Tests\Unit\DependencyInjection;

use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\CushonHealthBundle;
use Cushon\HealthBundle\DependencyInjection\CushonHealthExtension;
use Cushon\HealthBundle\Traits\BundleRootKeyTrait;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Tests\Utils\Constants;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class CushonHealthExtensionTest extends TestCase
{
    use ProphecyTrait;
    use BundleRootKeyTrait;

    private ?Filesystem $filesystem;
    private ?string $fixturesDir;

    public function testItRegistersTagForAutoconfiguration(): void
    {
        $rootKey = 'foo';
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter(
            'cushon_health.bundle_path',
            CushonHealthBundle::BUNDLE_PATH
        );

        $fileLoader = new FileLocator(CushonHealthBundle::CONFIG_PATH);

        $extension = new CushonHealthExtension(
            $fileLoader,
            $rootKey
        );

        $extension->load([], $containerBuilder);

        $autoConfiguration = $containerBuilder->getAutoconfiguredInstanceof();

        $this->assertArrayHasKey(DependencyCheck::class, $autoConfiguration);
        $definition = $autoConfiguration[DependencyCheck::class];
        $this->assertArrayHasKey(sprintf(
            '%s.%s',
            $rootKey,
            CushonHealthExtension::KEY_DEPENDENCY_CHECKS
        ), $definition->getTags());
    }

    public function testItLoadsServices(): void
    {
        $rootKey = 'foo';
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter(
            'cushon_health.bundle_path',
            CushonHealthBundle::BUNDLE_PATH
        );

        $servicesPath = $this->createBlankConfigFile('services.yaml');

        $fileLoader = $this->prophesize(FileLocator::class);
        $fileLoader->locate('services.yaml')->willReturn($servicesPath);

        $extension = new CushonHealthExtension(
            $fileLoader->reveal(),
            $rootKey
        );

        $extension->load([], $containerBuilder);


        $fileLoader->locate('services.yaml')->shouldHaveBeenCalledOnce();
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->fixturesDir = sprintf(
            '%s/%s/%d',
            Constants::buildDir(),
            'fixtures/config',
            (new DateTimeImmutable())->getTimestamp()
        );
        $this->filesystem->mkdir($this->fixturesDir);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->filesystem->remove(dirname($this->fixturesDir, 2));
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function createBlankConfigFile(string $fileName): string
    {
        $filePath = sprintf(
            '%s/%s',
            $this->fixturesDir,
            $fileName
        );

        $this->filesystem->touch($filePath);

        return $filePath;
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\DependencyInjection;

use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\CushonHealthBundle;
use Cushon\HealthBundle\DependencyInjection\CushonHealthExtension;
use Cushon\HealthBundle\Traits\BundleRootKeyTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CushonHealthExtensionTest extends TestCase
{
    use ProphecyTrait;
    use BundleRootKeyTrait;

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
}

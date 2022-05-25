<?php

declare(strict_types=1);

namespace Tests\Unit;

use Cushon\HealthBundle\CushonHealthBundle;
use Cushon\HealthBundle\DependencyInjection\Compiler\DependencyCheckPass;
use Cushon\HealthBundle\DependencyInjection\CushonHealthExtension;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CushonHealthBundleTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsTheBundleExtension(): void
    {
        $bundle = new CushonHealthBundle();

        $extension = $bundle->getContainerExtension();

        $this->assertInstanceOf(CushonHealthExtension::class, $extension);
    }

    public function testItAddsTheBundlePathToTeContainerBuilder(): void
    {
        $containerBuilder = new ContainerBuilder();

        $bundle = new CushonHealthBundle();
        $bundle->build($containerBuilder);

        $this->assertTrue($containerBuilder->hasParameter('cushon_health.bundle_path'));
    }
}

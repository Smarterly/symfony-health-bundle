<?php

declare(strict_types=1);

namespace Tests\Unit\Message\QueryFactory;

use Cushon\HealthBundle\Message\Query\HealthCheck;
use Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck;
use Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception\IncorrectQueryType;
use Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception\QueryServiceDefinitionNotFound;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class DefaultHealthCheckQueryTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsAHealthCheckQuery(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $healthCheck = $this->prophesize(HealthCheck::class)->reveal();
        $container->has(HealthCheck::class)->willReturn(true);
        $container->get(HealthCheck::class)->willReturn($healthCheck);

        $serviceContainerHealthCheck = new ServiceContainerHealthCheck($container->reveal());

        $this->assertSame($healthCheck, $serviceContainerHealthCheck->createQuery());
    }

    public function testItThrowsAnExceptionIfTheServiceContainerDoesNotContainAServiceDefinition(): void
    {
        $def = 'foo.wibble';
        $container = $this->prophesize(ContainerInterface::class);
        $container->has($def)->willReturn(false);
        $container->get($def)->willReturn(null);
        $cont = $container->reveal();
        $serviceContainerHealthCheck = new ServiceContainerHealthCheck(
            $cont,
            $def
        );
        $this->expectExceptionObject(QueryServiceDefinitionNotFound::create(
            $cont,
            $def
        ));

        $serviceContainerHealthCheck->createQuery();
    }

    public function testItThrowsAnExceptionIfTheServiceContainerDoesNotReturnAHealthCheck(): void
    {
        $incorrectReturnObject = new stdClass();
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(HealthCheck::class)->willReturn(true);
        $container->get(HealthCheck::class)->willReturn($incorrectReturnObject);
        $serviceContainerHealthCheck = new ServiceContainerHealthCheck($container->reveal());
        $this->expectExceptionObject(IncorrectQueryType::create(
            HealthCheck::class,
            $incorrectReturnObject
        ));

        $serviceContainerHealthCheck->createQuery();
    }
}

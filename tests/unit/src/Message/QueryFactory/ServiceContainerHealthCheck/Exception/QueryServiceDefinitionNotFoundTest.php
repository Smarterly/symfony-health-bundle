<?php

declare(strict_types=1);

namespace Tests\Unit\Message\QueryFactory\ServiceContainerHealthCheck\Exception;

use Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception\QueryServiceDefinitionNotFound;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class QueryServiceDefinitionNotFoundTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsTheContainer(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();

        $queryServiceDefinitionNotFound = QueryServiceDefinitionNotFound::create(
            $container,
            'foo-bar'
        );

        $this->assertSame($container, $queryServiceDefinitionNotFound->getContainer());
    }
}

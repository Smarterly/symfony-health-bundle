<?php

declare(strict_types=1);

namespace Tests\Unit\Message\QueryFactory\ServiceContainerHealthCheck\Exception;

use Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception\QueryServiceDefinitionNotFound;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class QueryServiceDefinitionNotFoundTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsTheContainer(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $definitionName = 'foo-bar';

        $queryServiceDefinitionNotFound = QueryServiceDefinitionNotFound::create(
            $container,
            $definitionName
        );

        $this->assertSame($container, $queryServiceDefinitionNotFound->getContainer());
        $this->assertStringContainsString(
            $definitionName,
            $queryServiceDefinitionNotFound->getMessage()
        );
    }
}

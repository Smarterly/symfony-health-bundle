<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console\StyledOutput\Dependency\DependencyTable;

use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\SimpleMapper;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\TableHeaderFactory;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Helper\TableCell;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class TableHeaderFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsAHealthyTableHeaderRow(): void
    {
        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(true);

        $tableHeaderFactory = new TableHeaderFactory(new SimpleMapper());

        $tableCells = iterator_to_array($tableHeaderFactory->createHeaders($healthCheck->reveal()));
        $this->assertArrayHasKey(SimpleMapper::KEY_NAME, $tableCells);

        $dependency = $tableCells[SimpleMapper::KEY_NAME];
        $options = $dependency->getStyle()->getOptions();
        $this->assertSame(TableHeaderFactory::DEFAULT_COLOUR_HEALTHY, $options['fg']);
    }

    public function testItReturnsAUnhealthyTableHeaderRow(): void
    {
        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(false);

        $tableHeaderFactory = new TableHeaderFactory(new SimpleMapper());

        $tableCells = iterator_to_array($tableHeaderFactory->createHeaders($healthCheck->reveal()));
        $this->assertArrayHasKey(SimpleMapper::KEY_HEALTH, $tableCells);
        /** @var TableCell $depdendency */
        $depdendency = $tableCells[SimpleMapper::KEY_HEALTH];
        $options = $depdendency->getStyle()->getOptions();
        $this->assertSame(TableHeaderFactory::DEFAULT_COLOUR_UNHEALTHY, $options['fg']);
    }

    public function testTheDefaultColoursCanBeChanged(): void
    {
        $healthyColour = 'blue';
        $unhealthyColour = 'yellow';

        $tableHeaderFactory = new TableHeaderFactory(
            new SimpleMapper(),
            $healthyColour,
            $unhealthyColour
        );

        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(true);

        $tableCells = iterator_to_array($tableHeaderFactory->createHeaders($healthCheck->reveal()));
        $this->assertArrayHasKey(SimpleMapper::KEY_INFO, $tableCells);
        /** @var TableCell $depdendency */
        $depdendency = $tableCells[SimpleMapper::KEY_INFO];
        $options = $depdendency->getStyle()->getOptions();
        $this->assertSame($healthyColour, $options['fg']);

        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(false);

        $tableCells = iterator_to_array($tableHeaderFactory->createHeaders($healthCheck->reveal()));
        $depdendency = $tableCells[SimpleMapper::KEY_HEALTH];
        $options = $depdendency->getStyle()->getOptions();
        $this->assertSame($unhealthyColour, $options['fg']);
    }
}

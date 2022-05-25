<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper;
use Generator;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

final class DependencyRowFactory
{
    public const DEFAULT_COLOUR_HEALTHY = 'green';
    public const DEFAULT_COLOUR_UNHEALTHY = 'red';

    private string $healthyColour;
    private string $unhealthyColour;
    private Mapper $dependencyMapper;

    /**
     * @param string $healthyColour
     * @param string $unhealthyColour
     */
    public function __construct(
        Mapper $dependencyMapper,
        string $healthyColour = self::DEFAULT_COLOUR_HEALTHY,
        string $unhealthyColour = self::DEFAULT_COLOUR_UNHEALTHY
    ) {
        $this->dependencyMapper = $dependencyMapper;
        $this->healthyColour = $healthyColour;
        $this->unhealthyColour = $unhealthyColour;
    }

    /**
     * @param DependencyStatus $dependencyStatus
     * @return Generator
     */
    public function createDependencyRow(DependencyStatus $dependencyStatus): Generator
    {
        $tableCellStyle = $this->createTableCellStyle($dependencyStatus);
        foreach ($this->getStatusData($dependencyStatus) as $name => $text) {
            yield $name => new TableCell($text, ['style' => $tableCellStyle]);
        }
    }

    /**
     * @param DependencyStatus $dependencyStatus
     * @return Generator
     */
    private function getStatusData(DependencyStatus $dependencyStatus): Generator
    {
        yield from $this->dependencyMapper->mapDependencyStatus($dependencyStatus);
    }

    /**
     * @param DependencyStatus $dependencyStatus
     * @return TableCellStyle
     */
    private function createTableCellStyle(DependencyStatus $dependencyStatus): TableCellStyle
    {
        return new TableCellStyle([
            'fg' => $dependencyStatus->isHealthy() ? $this->healthyColour : $this->unhealthyColour,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable;

use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Generator;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class TableHeaderFactory
{
    public const DEFAULT_COLOUR_HEALTHY = 'green';
    public const DEFAULT_COLOUR_UNHEALTHY = 'yellow';

    private string $healthyColour;
    private string $unhealthyColour;

    private Mapper $dependencyMapper;

    /**
     * @param Mapper $dependencyMapper
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
     * @param HealthCheck $healthCheck
     * @return Generator<string, TableCell, int, void>
     */
    public function createHeaders(HealthCheck $healthCheck): Generator
    {
        $tableCellStyle = $this->createTableCellStyle($healthCheck);

        foreach ($this->getHealthCheckHeaders($healthCheck) as $name => $text) {
            yield $name => new TableCell($text, ['style' => $tableCellStyle]);
        }
    }

    /**
     * @param HealthCheck $healthCheck
     * @return TableCellStyle
     */
    private function createTableCellStyle(HealthCheck $healthCheck): TableCellStyle
    {
        return new TableCellStyle([
            'fg' => $healthCheck->isHealthy() ? $this->healthyColour : $this->unhealthyColour,
        ]);
    }

    /**
     * @param HealthCheck $healthCheck
     * @return iterable<string, string>
     */
    private function getHealthCheckHeaders(HealthCheck $healthCheck): iterable
    {
        return $this->dependencyMapper->getHealthCheckHeaders($healthCheck);
    }
}

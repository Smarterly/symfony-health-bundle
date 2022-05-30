<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\Dependencies\HealthReportFactory;

use Cushon\HealthBundle\ApplicationHealth\Dependencies\Simpledependencies\HealthReportFactory;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\SimpleHealthReport;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class SimpleReportFactory implements HealthReportFactory
{
    /**
     * @param bool $health
     * @param DependencyStatus ...$dependencyStatuses
     * @return SimpleHealthReport
     */
    public function generateReport(bool $health, DependencyStatus ...$dependencyStatuses): SimpleHealthReport
    {
        return new SimpleHealthReport(
            $health,
            ...$dependencyStatuses
        );
    }
}

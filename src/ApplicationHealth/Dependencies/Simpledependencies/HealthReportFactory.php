<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\Dependencies\Simpledependencies;

use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface HealthReportFactory
{
    /**
     * @param bool $health
     * @param DependencyStatus ...$dependencyStatuses
     * @return HealthReport
     */
    public function generateReport(bool $health, DependencyStatus ...$dependencyStatuses): HealthReport;
}

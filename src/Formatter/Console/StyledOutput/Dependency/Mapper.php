<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck;

interface Mapper
{
    /**
     * @param HealthCheck $healthCheck
     * @return iterable<string, string>
     */
    public function getHealthCheckHeaders(HealthCheck $healthCheck): iterable;

    /**
     * @param DependencyStatus $dependencyStatus
     * @return iterable<string, string>
     */
    public function mapDependencyStatus(DependencyStatus $dependencyStatus): iterable;
}

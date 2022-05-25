<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus;

use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;

interface HealthCheckQueryBus
{
    /**
     * @param HealthCheckQuery $healthCheckQuery
     * @return HealthCheckResult
     */
    public function handleHealthCheckQuery(HealthCheckQuery $healthCheckQuery): HealthCheckResult;
}

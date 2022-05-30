<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Handler\CheckHealth;

use Cushon\HealthBundle\Handler\CheckHealth\Exception\CheckHealthHandlerError;
use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface Logger
{
    /**
     * @param HealthCheckQuery $healthCheckQuery
     * @return void
     */
    public function begin(HealthCheckQuery $healthCheckQuery): void;

    /**
     * @param HealthCheckResult $healthCheckResult
     * @return void
     */
    public function complete(HealthCheckResult $healthCheckResult): void;

    /**
     * @param CheckHealthHandlerError $exc
     * @return void
     */
    public function error(CheckHealthHandlerError $exc): void;
}

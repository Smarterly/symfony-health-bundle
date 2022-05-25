<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Handler;

use Cushon\HealthBundle\Handler\CheckHealth\Exception\CheckHealthHandlerError;
use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

interface CheckHealth extends MessageHandlerInterface
{
    /**
     * @param HealthCheckQuery $healthCheck
     * @return HealthCheckResult
     * @throws CheckHealthHandlerError should there be an issue in checking health.
     */
    public function __invoke(HealthCheckQuery $healthCheck): HealthCheckResult;
}

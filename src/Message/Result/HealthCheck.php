<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\Result;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use JsonSerializable;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface HealthCheck extends JsonSerializable
{
    public const STATUS_HEALTHY = 'healthy';
    public const STATUS_UNHEALTHY = 'unhealthy';

    public const KEY_STATUS = 'status';
    public const KEY_DEPENDENCIES = 'dependencies';

    /**
     * @return iterable<DependencyStatus>
     */
    public function dependencies(): iterable;

    /**
     * @return bool
     */
    public function isHealthy(): bool;
}

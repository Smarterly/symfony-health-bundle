<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface HealthReport
{
    /**
     * @return bool
     */
    public function isHealthy(): bool;

    /**
     * @return iterable<DependencyStatus>
     */
    public function dependencies(): iterable;
}

<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;

interface DependencyCheck
{
    /**
     * @return DependencyStatus
     */
    public function check(): DependencyStatus;
}

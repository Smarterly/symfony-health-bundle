<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Generator;

interface DependencyCheck
{
    /**
     * @return Generator<int, DependencyStatus, int, void>
     */
    public function check(): Generator;
}

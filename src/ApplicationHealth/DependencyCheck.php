<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Generator;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface DependencyCheck
{
    /**
     * @return Generator<int, DependencyStatus, int, void>
     */
    public function check(): Generator;
}

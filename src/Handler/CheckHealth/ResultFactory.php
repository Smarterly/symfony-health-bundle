<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Handler\CheckHealth;

use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Cushon\HealthBundle\Message\Result\HealthCheck;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface ResultFactory
{
    /**
     * @param HealthReport $health
     * @return HealthCheck
     */
    public function fromHealth(HealthReport $health): HealthCheck;
}

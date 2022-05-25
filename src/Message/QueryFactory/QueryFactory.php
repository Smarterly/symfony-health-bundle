<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\QueryFactory;

use Cushon\HealthBundle\Message\Query\HealthCheck;

interface QueryFactory
{
    /**
     * @return HealthCheck
     */
    public function createQuery(): HealthCheck;
}

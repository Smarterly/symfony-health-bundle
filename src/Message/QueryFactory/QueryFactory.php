<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\QueryFactory;

use Cushon\HealthBundle\Message\Query\HealthCheck;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface QueryFactory
{
    /**
     * @return HealthCheck
     */
    public function createQuery(): HealthCheck;
}

<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter;

use Cushon\HealthBundle\Message\Result\HealthCheck;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface Console
{
    /**
     * @param HealthCheck $healthCheck
     * @return void
     */
    public function format(HealthCheck $healthCheck): void;
}

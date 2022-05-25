<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter;

use Cushon\HealthBundle\Message\Result\HealthCheck;

interface Console
{
    /**
     * @param HealthCheck $healthCheck
     * @return void
     */
    public function format(HealthCheck $healthCheck): void;
}

<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput;

use Cushon\HealthBundle\Message\Result\HealthCheck;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface ApplicationStatus
{
    public const STATUS_HEALTHY = 'Healthy';
    public const STATUS_UNHEALTHY = 'Unhealthy';

    /**
     * @param HealthCheck $healthCheck
     * @param SymfonyStyle $styler
     * @return void
     */
    public function format(HealthCheck $healthCheck, SymfonyStyle $styler): void;
}

<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput\ApplicationStatus;

use Cushon\HealthBundle\Formatter\Console\StyledOutput\ApplicationStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class StatusSection implements ApplicationStatus
{
    public const DEFAULT_HEALTHY = 'Status: %s';
    public const DEFAULT_UNHEALTHY = 'Status: %s!';

    private string $healthy;
    private string $unhealthy;

    /**
     * @param string $healthy
     * @param string $unhealthy
     */
    public function __construct(
        string $healthy = self::DEFAULT_HEALTHY,
        string $unhealthy = self::DEFAULT_UNHEALTHY
    ) {
        $this->healthy = $healthy;
        $this->unhealthy = $unhealthy;
    }

    /**
     * @inheritDoc
     */
    public function format(HealthCheck $healthCheck, SymfonyStyle $styler): void
    {
        $styler->section('Application Health');
        if ($healthCheck->isHealthy()) {
            $styler->success($this->getHealthy());
        } else {
            $styler->warning($this->getUnhealthy());
        }
    }

    /**
     * @return string
     */
    private function getHealthy(): string
    {
        return sprintf($this->healthy, self::STATUS_HEALTHY);
    }

    /**
     * @return string
     */
    private function getUnhealthy(): string
    {
        return sprintf($this->unhealthy, self::STATUS_UNHEALTHY);
    }
}

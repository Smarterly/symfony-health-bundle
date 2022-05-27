<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Formatter\Console;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\ApplicationStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

final class StyledOutput implements Console
{
    public const STATUS_HEALTHY = 'Healthy';
    public const STATUS_UNHEALTHY = 'Unhealthy';

    private SymfonyStyle $styler;
    private Dependency $dependencyFormatter;
    private ApplicationStatus $applicationStatusFormatter;

    /**
     * @param SymfonyStyle $styler
     * @param ApplicationStatus $applicationStatusFormatter
     * @param Dependency $dependencyFormatter
     */
    public function __construct(
        SymfonyStyle $styler,
        ApplicationStatus $applicationStatusFormatter,
        Dependency $dependencyFormatter
    ) {
        $this->styler = $styler;
        $this->dependencyFormatter = $dependencyFormatter;
        $this->applicationStatusFormatter = $applicationStatusFormatter;
    }

    /**
     * @inheritDoc
     */
    public function format(HealthCheck $healthCheck): void
    {
        $healthCheckStyler = $this->getHealthReportStyler($healthCheck);

        $this->applicationStatusFormatter->format($healthCheck, $healthCheckStyler);
        $this->dependencyFormatter->format($healthCheck, $healthCheckStyler);
    }

    /**
     * @param HealthCheck $healthCheck
     * @return SymfonyStyle
     */
    private function getHealthReportStyler(HealthCheck $healthCheck): SymfonyStyle
    {
        return $healthCheck->isHealthy() ? $this->styler : $this->styler->getErrorStyle();
    }
}

<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\Dependencies;

use Cushon\HealthBundle\ApplicationHealth\Dependencies;
use Cushon\HealthBundle\ApplicationHealth\Dependencies\Exception\NoDependencyChecks;
use Cushon\HealthBundle\ApplicationHealth\Dependencies\Simpledependencies\HealthReportFactory;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Ds\Set;
use Generator;

final class SimpleDependencies implements Dependencies
{
    /**
     * @var Set<DependencyCheck>
     */
    private Set $dependencyChecks;
    private HealthReportFactory $healthReportFactory;

    /**
     * @param HealthReportFactory $healthReportFactory
     * @param iterable<DependencyCheck> $dependencyChecks
     * @return static
     */
    public static function fromIterable(
        HealthReportFactory $healthReportFactory,
        iterable $dependencyChecks
    ): self {
        return new self($healthReportFactory, ...$dependencyChecks);
    }

    /**
     * @param HealthReportFactory $healthReportFactory
     * @param DependencyCheck ...$dependencyChecks
     */
    public function __construct(
        HealthReportFactory $healthReportFactory,
        DependencyCheck ...$dependencyChecks
    ) {
        if (!count($dependencyChecks)) {
            throw NoDependencyChecks::create();
        }
        $this->healthReportFactory = $healthReportFactory;
        $this->dependencyChecks = new Set($dependencyChecks);
    }

    /**
     * @inheritDoc
     */
    public function check(): HealthReport
    {
        $healthy = true;
        $dependencyStatuses = [];
        foreach ($this->checkDependencies() as $dependencyStatus) {
            $dependencyStatuses[] = $dependencyStatus;
            if (!$dependencyStatus->isHealthy()) {
                $healthy = false;
            }
        }

        return $this->healthReportFactory->generateReport($healthy, ...$dependencyStatuses);
    }

    /**
     * @return Generator
     * @psalm-return Generator<int, DependencyStatus, mixed, void>
     */
    private function checkDependencies(): Generator
    {
        foreach ($this->dependencyChecks as $dependencyCheck) {
            yield $dependencyCheck->check();
        }
    }
}

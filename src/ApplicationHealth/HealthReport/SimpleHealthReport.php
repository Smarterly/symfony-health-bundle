<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\HealthReport;

use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Ds\Set;
use Generator;

final class SimpleHealthReport implements HealthReport
{
    private bool $healthy;

    /**
     * @var Set<DependencyStatus>
     */
    private Set $dependencyStatuses;

    /**
     * @param bool $healthy
     * @param DependencyStatus ...$dependencyStatuses
     */
    public function __construct(bool $healthy, DependencyStatus ...$dependencyStatuses)
    {
        if (!count($dependencyStatuses)) {
            throw HealthReport\Exception\NoDependencyStatuses::create();
        }

        $this->healthy = $healthy;
        $this->dependencyStatuses = new Set($dependencyStatuses);
    }

    /**
     * @inheritDoc
     */
    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    /**
     * @inheritDoc
     *
     * @psalm-return Generator<int, DependencyStatus, mixed, void>
     */
    public function dependencies(): Generator
    {
        foreach ($this->dependencyStatuses as $dependencyStatus) {
            yield $dependencyStatus;
        }
    }
}

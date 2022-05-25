<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\Result\Traits;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Ds\Set;
use Generator;

trait DependencyTrait
{
    /**
     * @var Set<DependencyStatus>
     */
    private Set $dependencies;

    /**
     * @psalm-return Generator<int, DependencyStatus, mixed, void>
     * @return iterable<DependencyStatus>
     */
    public function dependencies(): iterable
    {
        foreach ($this->dependencies as $dependency) {
            yield $dependency;
        }
    }
}

<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\Result\Traits;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
trait JsonSerializableTrait
{
    /**
     * @return (DependencyStatus[]|string)[]
     *
     * @psalm-return array{status: string, dependencies: list<DependencyStatus>}
     */
    public function jsonSerialize(): array
    {
        $dependencies = [];

        foreach ($this->dependencies() as $dependency) {
            $dependencies[] = $dependency;
        }

        return [
            HealthCheck::KEY_STATUS => $this->getStatus(),
            HealthCheck::KEY_DEPENDENCIES => $dependencies,
        ];
    }

    /**
     * @return iterable<DependencyStatus>
     */
    abstract public function dependencies(): iterable;

    /**
     * @return string
     */
    abstract private function getStatus(): string;
}

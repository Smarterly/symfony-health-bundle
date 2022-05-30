<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\Result\HealthCheck;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\Message\Result\Traits\DependencyTrait;
use Cushon\HealthBundle\Message\Result\Traits\JsonSerializableTrait;
use Ds\Set;
use Generator;

final class Unhealthy implements HealthCheck
{
    use DependencyTrait;
    use JsonSerializableTrait;

    /**
     * @param DependencyStatus ...$dependencies
     */
    public function __construct(DependencyStatus ...$dependencies)
    {
        $this->dependencies = new Set($dependencies);
    }

    /**
     * @return iterable<DependencyStatus>
     * @psalm-return Generator<int, DependencyStatus, mixed, void>
     */
    public function __invoke(): iterable
    {
        return $this->dependencies();
    }

    /**
     * @inheritDoc
     */
    public function isHealthy(): bool
    {
        return false;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod) Comes from the Trait which PHPMD cannot detect
     * @psalm-return 'unhealthy'
     */
    private function getStatus(): string
    {
        return self::STATUS_UNHEALTHY;
    }
}

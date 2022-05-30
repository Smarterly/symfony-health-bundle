<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\Exception\UnableToMapDependency;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Generator;

final class SimpleMapper implements Mapper
{
    public const KEY_NAME = 'name';
    public const KEY_HEALTH = 'health';
    public const KEY_INFO = 'info';

    private string $name;
    private string $health;
    private string $info;

    /**
     * @param string $name
     * @param string $health
     * @param string $info
     */
    public function __construct(string $name = 'Name', string $health = 'Status', string $info = 'Info')
    {
        $this->name = $name;
        $this->health = $health;
        $this->info = $info;
    }

    /**
     * @inheritDoc
     */
    public function getHealthCheckHeaders(HealthCheck $healthCheck): iterable
    {
        yield from $this->yieldMap($this->name, $this->health, $this->info);
    }

    /**
     * @inheritDoc
     */
    public function mapDependencyStatus(DependencyStatus $dependencyStatus): iterable
    {
        if (!$dependencyStatus instanceof SimpleStatus) {
            throw UnableToMapDependency::create($this, $dependencyStatus);
        }

        return $this->mapSimpleDependency($dependencyStatus);
    }

    /**
     * @param SimpleStatus $simpleStatus
     * @return Generator<string, string, int, void>
     */
    private function mapSimpleDependency(SimpleStatus $simpleStatus): Generator
    {
        $statusInfo = (string) $simpleStatus->getInfo();
        yield from $this->yieldMap(
            $simpleStatus->getName(),
            $this->getStatusHealth($simpleStatus),
            $statusInfo
        );
    }

    /**
     * @param string $name
     * @param string $health
     * @param string $info
     * @return Generator
     * @psalm-return Generator<'health'|'info'|'name', string, mixed, void>
     */
    private function yieldMap(string $name, string $health, string $info): Generator
    {
        yield self::KEY_NAME => $name;
        yield self::KEY_HEALTH => $health;
        yield self::KEY_INFO => $info;
    }

    /**
     * @param DependencyStatus $status
     * @return string
     */
    private function getStatusHealth(DependencyStatus $status): string
    {
        return ($status->isHealthy()) ? Dependency::STATUS_HEALTHY : Dependency::STATUS_UNHEALTHY;
    }
}

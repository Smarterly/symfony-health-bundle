<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class SimpleStatus implements DependencyStatus
{
    public const KEY_INFO = 'info';

    private string $name;

    private bool $healthy;

    private mixed $info;

    /**
     * @param string $name
     * @param bool $healthy
     * @param mixed|null $info
     */
    public function __construct(string $name, bool $healthy, mixed $info = null)
    {
        $this->name = $name;
        $this->healthy = $healthy;
        $this->info = $info;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    /**
     * @return mixed
     */
    public function getInfo(): mixed
    {
        return $this->info;
    }

    /**
     * @return array{'name':string,'healthy': bool, 'info': mixed}
     */
    public function jsonSerialize(): array
    {
        return [
            self::KEY_NAME => $this->getName(),
            self::KEY_HEALTHY => $this->isHealthy(),
            self::KEY_INFO => $this->getInfo(),
        ];
    }
}

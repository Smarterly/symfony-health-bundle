<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\HealthReport;

use JsonSerializable;

interface DependencyStatus extends JsonSerializable
{
    public const KEY_NAME = 'name';

    public const KEY_HEALTHY = 'healthy';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isHealthy(): bool;
}

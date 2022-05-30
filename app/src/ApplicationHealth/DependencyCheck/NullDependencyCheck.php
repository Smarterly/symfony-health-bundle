<?php

declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck;

use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Generator;

final class NullDependencyCheck implements DependencyCheck
{
    private string $name;

    private bool $health;

    private string $info;

    /**
     * @param string $name
     * @param bool $health
     * @param string $info
     */
    public function __construct(string $name, bool $health, string $info)
    {
        $this->name = $name;
        $this->health = $health;
        $this->info = $info;
    }

    /**
     * @inheritDoc
     */
    public function check(): Generator
    {
        yield new DependencyStatus\SimpleStatus(
            $this->name,
            $this->health,
            $this->info
        );
    }
}

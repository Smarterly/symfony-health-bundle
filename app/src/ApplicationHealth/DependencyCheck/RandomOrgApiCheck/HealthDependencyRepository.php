<?php

declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck;

interface HealthDependencyRepository
{
    /**
     * @return int
     */
    public function fetchRandomNumber(): int;
}

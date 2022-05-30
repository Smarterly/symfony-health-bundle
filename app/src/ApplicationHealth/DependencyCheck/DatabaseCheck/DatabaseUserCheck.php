<?php

declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck\DatabaseCheck;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;

interface DatabaseUserCheck
{
    /**
     * @return DependencyStatus
     */
    public function checkUser(): DependencyStatus;
}

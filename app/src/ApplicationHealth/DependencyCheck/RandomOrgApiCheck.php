<?php

declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck;

use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\Exception\HealthDependencyException;
use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\HealthDependencyRepository;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;

final class RandomOrgApiCheck implements DependencyCheck
{
    private HealthDependencyRepository $healthDependencyRepository;

    /**
     * @param HealthDependencyRepository $healthDependencyRepository
     */
    public function __construct(HealthDependencyRepository $healthDependencyRepository)
    {
        $this->healthDependencyRepository = $healthDependencyRepository;
    }


    /**
     * @return DependencyStatus
     */
    public function check(): DependencyStatus
    {
        $health = false;

        try {
            $randomNumber = $this->healthDependencyRepository->fetchRandomNumber();
            $health = true;
            $info = sprintf('All OK, random number was %d', $randomNumber);
        } catch (HealthDependencyException $e) {
            $info = $e->getMessage();
        }

        return new SimpleStatus('random.org', $health, $info);
    }
}

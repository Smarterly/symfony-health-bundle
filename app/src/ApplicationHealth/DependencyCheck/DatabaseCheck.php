<?php
declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck;

use App\ApplicationHealth\DependencyCheck\DatabaseCheck\HealthDependencyRepository;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Ds\Set;
use Generator;

final class DatabaseCheck implements DependencyCheck
{
    private Set $dependencyRepositories;

    /**
     * @param HealthDependencyRepository ...$dependencyRepositories
     */
    public function __construct(HealthDependencyRepository ...$dependencyRepositories)
    {
        $this->dependencyRepositories = new Set($dependencyRepositories);
    }

    /**
     * @inheritDoc
     */
    public function check(): Generator
    {
        /** @var HealthDependencyRepository $dependencyRepository */
        foreach ($this->dependencyRepositories as $dependencyRepository) {
            yield $dependencyRepository->checkUser();
        }
    }
}

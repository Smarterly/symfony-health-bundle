<?php

declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck;

use App\ApplicationHealth\DependencyCheck\DatabaseCheck\DatabaseUserCheck;
use App\ApplicationHealth\DependencyCheck\DatabaseCheck\Exception\NoDatabaseChecksProvided;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Ds\Set;
use Generator;

final class DatabaseCheck implements DependencyCheck
{
    /**
     * @var Set<DatabaseUserCheck>
     */
    private Set $dependencyRepositories;

    /**
     * @param iterable<DatabaseUserCheck> $dependencyRepositories
     */
    public function __construct(iterable $dependencyRepositories)
    {
        $this->dependencyRepositories = new Set();
        foreach ($dependencyRepositories as $dependencyRepository) {
            $this->addDatabaseUserCheck($dependencyRepository);
        }

        if (!$this->dependencyRepositories->count()) {
            throw NoDatabaseChecksProvided::create();
        }
    }

    /**
     * @inheritDoc
     */
    public function check(): Generator
    {
        foreach ($this->dependencyRepositories as $dependencyRepository) {
            yield $dependencyRepository->checkUser();
        }
    }

    /**
     * @param DatabaseUserCheck $databaseUserChecks
     * @return void
     */
    private function addDatabaseUserCheck(DatabaseUserCheck $databaseUserChecks): void
    {
        $this->dependencyRepositories->add($databaseUserChecks);
    }
}

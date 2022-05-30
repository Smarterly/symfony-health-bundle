<?php

declare(strict_types=1);

namespace App\Repository\DatabaseCheck;

use App\ApplicationHealth\DependencyCheck\DatabaseCheck\DatabaseUserCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;

final class WriteUserCheck implements DatabaseUserCheck
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @param Connection $writeConnection
     */
    public function __construct(Connection $writeConnection)
    {
        $this->connection = $writeConnection;
    }

    /**
     * @inheritDoc
     */
    public function checkUser(): DependencyStatus
    {
        $health = false;
        try {
            $this->connection->createQueryBuilder()
                ->insert('health')
                ->setValue('last_checked', ':now')
                ->setParameter('now', new DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
                ->executeStatement();
            $health = true;
            $info = null;
        } catch (Exception $e) {
            $info = $e->getMessage();
        }

        return new SimpleStatus('DB write user', $health, $info);
    }
}

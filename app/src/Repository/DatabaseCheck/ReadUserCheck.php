<?php

declare(strict_types=1);

namespace App\Repository\DatabaseCheck;

use App\ApplicationHealth\DependencyCheck\DatabaseCheck\DatabaseUserCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class ReadUserCheck implements DatabaseUserCheck
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @param Connection $readConnection
     */
    public function __construct(Connection $readConnection)
    {
        $this->connection = $readConnection;
    }

    /**
     * @inheritDoc
     */
    public function checkUser(): DependencyStatus
    {
        $healthy = false;
        try {
            $this->connection->createQueryBuilder()
                ->select('last_checked')
                ->from('health')
                ->executeQuery();
            $healthy = true;
            $info = null;
        } catch (Exception $e) {
            $info = $e->getMessage();
        }

        return new SimpleStatus('DB read user', $healthy, $info);
    }
}

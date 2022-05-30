<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Doctrine\UuidBinaryType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530155854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create a simple table for health checks';
    }

    public function up(Schema $schema): void
    {
        $health = $schema->createTable('health');
        $health->addColumn(
            'last_checked',
            Types::DATETIME_IMMUTABLE
        );

    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('health');
    }
}

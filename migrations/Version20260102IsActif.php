<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260102IsActif extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Description de la migration, par exemple : ajout de is_active à user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD is_active TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP COLUMN is_active');
    }
}

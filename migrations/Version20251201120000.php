<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251201120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprime la colonne profil de la table user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP COLUMN profil');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD profil LONGTEXT NOT NULL');
    }
}

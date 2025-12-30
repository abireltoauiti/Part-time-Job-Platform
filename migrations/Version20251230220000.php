<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251230220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes telephone et adresse dans user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD `telephone` VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD `adresse` VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP `telephone`');
        $this->addSql('ALTER TABLE `user` DROP `adresse`');
    }
}

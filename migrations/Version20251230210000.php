<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251230210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs du candidat dans user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD `cv` VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD `competences` TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD `experience` TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD `formation` TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD `date_naissance` DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP `cv`');
        $this->addSql('ALTER TABLE `user` DROP `competences`');
        $this->addSql('ALTER TABLE `user` DROP `experience`');
        $this->addSql('ALTER TABLE `user` DROP `formation`');
        $this->addSql('ALTER TABLE `user` DROP `date_naissance`');
    }
}

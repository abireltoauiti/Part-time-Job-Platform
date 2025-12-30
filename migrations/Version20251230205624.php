<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230205624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Ajouter le champ is_profile_complete à la table user
        $this->addSql('ALTER TABLE `user` ADD `is_profile_complete` TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // Supprimer le champ si on veut revenir en arrière
        $this->addSql('ALTER TABLE `user` DROP `is_profile_complete`');
    }
}

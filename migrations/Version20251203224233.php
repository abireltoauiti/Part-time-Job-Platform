<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251203224233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY `FK_FBD8E0F8A4AEAFEA`');
        $this->addSql('DROP INDEX IDX_FBD8E0F8A4AEAFEA ON job');
        $this->addSql('ALTER TABLE job CHANGE entreprise_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8A76ED395 ON job (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8A76ED395');
        $this->addSql('DROP INDEX IDX_FBD8E0F8A76ED395 ON job');
        $this->addSql('ALTER TABLE job CHANGE user_id entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT `FK_FBD8E0F8A4AEAFEA` FOREIGN KEY (entreprise_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8A4AEAFEA ON job (entreprise_id)');
    }
}

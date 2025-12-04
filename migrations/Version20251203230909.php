<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251203230909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY `FK_JOB_USER`');
        $this->addSql('ALTER TABLE job CHANGE categorie_job_id categorie_job_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('DROP INDEX fk_job_user ON job');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8A76ED395 ON job (user_id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT `FK_JOB_USER` FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8A76ED395');
        $this->addSql('ALTER TABLE job CHANGE user_id user_id INT DEFAULT NULL, CHANGE categorie_job_id categorie_job_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX idx_fbd8e0f8a76ed395 ON job');
        $this->addSql('CREATE INDEX FK_JOB_USER ON job (user_id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}

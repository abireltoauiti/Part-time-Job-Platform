<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251127204442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, cv VARCHAR(255) NOT NULL, job_id INT NOT NULL, INDEX IDX_E33BD3B8BE04EA9 (job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE categorie_job (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, logo VARCHAR(255) DEFAULT NULL, secteur VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, localisation VARCHAR(255) NOT NULL, salaire INT NOT NULL, date_limite DATETIME NOT NULL, entreprise_id INT NOT NULL, categorie_job_id INT NOT NULL, INDEX IDX_FBD8E0F8A4AEAFEA (entreprise_id), INDEX IDX_FBD8E0F88DC6E490 (categorie_job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B8BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F88DC6E490 FOREIGN KEY (categorie_job_id) REFERENCES categorie_job (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B8BE04EA9');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8A4AEAFEA');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F88DC6E490');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE categorie_job');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

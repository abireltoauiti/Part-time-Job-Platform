<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260102CreateMessage extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table message';
    }

    public function up(Schema $schema): void
    {
        // Création de la table message
        $this->addSql('
            CREATE TABLE message (
                id INT AUTO_INCREMENT NOT NULL,
                sender_id INT NOT NULL,
                recipient_id INT NOT NULL,
                subject VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                is_read TINYINT(1) DEFAULT 0 NOT NULL,
                created_at DATETIME NOT NULL,
                PRIMARY KEY(id),
                CONSTRAINT FK_MESSAGE_SENDER FOREIGN KEY (sender_id) REFERENCES user (id),
                CONSTRAINT FK_MESSAGE_RECIPIENT FOREIGN KEY (recipient_id) REFERENCES user (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE message');
    }
}

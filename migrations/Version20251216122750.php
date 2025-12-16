<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251216122750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auteur CHANGE nationalite nationalite VARCHAR(255) DEFAULT NULL, CHANGE date_naissance date_naissance DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE commande CHANGE admin_note admin_note VARCHAR(255) DEFAULT NULL, CHANGE validated_at validated_at DATETIME DEFAULT NULL, CHANGE rejected_at rejected_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE livre CHANGE isbn isbn VARCHAR(255) DEFAULT NULL, CHANGE genre genre VARCHAR(100) DEFAULT NULL, CHANGE image_couverture image_couverture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation CHANGE date_reponse date_reponse DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auteur CHANGE nationalite nationalite VARCHAR(255) DEFAULT \'NULL\', CHANGE date_naissance date_naissance DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE commande CHANGE admin_note admin_note VARCHAR(255) DEFAULT \'NULL\', CHANGE validated_at validated_at DATETIME DEFAULT \'NULL\', CHANGE rejected_at rejected_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE livre CHANGE isbn isbn VARCHAR(255) DEFAULT \'NULL\', CHANGE genre genre VARCHAR(100) DEFAULT \'NULL\', CHANGE image_couverture image_couverture VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reclamation CHANGE date_reponse date_reponse DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\'');
    }
}

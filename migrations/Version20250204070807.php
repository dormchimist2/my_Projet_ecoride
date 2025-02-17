<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204070807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE preference_cdt (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, accepte_fumeurs TINYINT(1) NOT NULL, accepte_animaux_g VARCHAR(50) NOT NULL, accepte_animaux_p TINYINT(1) NOT NULL, musique_autorisee TINYINT(1) NOT NULL, discution_autorisee TINYINT(1) NOT NULL, siege_bebe TINYINT(1) NOT NULL, ad_preference VARCHAR(150) DEFAULT NULL, UNIQUE INDEX UNIQ_13410DECA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE preference_cdt ADD CONSTRAINT FK_13410DECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preference_cdt DROP FOREIGN KEY FK_13410DECA76ED395');
        $this->addSql('DROP TABLE preference_cdt');
    }
}

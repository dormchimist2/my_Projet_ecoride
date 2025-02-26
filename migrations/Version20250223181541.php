<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223181541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte DROP CONSTRAINT FK_CFF65260A76ED395');
        $this->addSql('DROP INDEX UNIQ_CFF65260A76ED395');
        $this->addSql('ALTER TABLE compte RENAME COLUMN userx_id TO user_id');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260A76ED395 FOREIGN KEY (user_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CFF65260A76ED395 ON compte (user_id)');
        $this->addSql('ALTER TABLE covoiturage DROP CONSTRAINT FK_28C79E89A76ED395');
        $this->addSql('DROP INDEX IDX_28C79E89A76ED395');
        $this->addSql('ALTER TABLE covoiturage RENAME COLUMN userx_id TO user_id');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89A76ED395 FOREIGN KEY (user_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_28C79E89A76ED395 ON covoiturage (user_id)');
        $this->addSql('ALTER TABLE preference_cdt DROP CONSTRAINT FK_13410DECA76ED395');
        $this->addSql('DROP INDEX UNIQ_13410DECA76ED395');
        $this->addSql('ALTER TABLE preference_cdt ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE preference_cdt DROP userx_id');
        $this->addSql('ALTER TABLE preference_cdt ADD CONSTRAINT FK_13410DECA76ED395 FOREIGN KEY (user_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_13410DECA76ED395 ON preference_cdt (user_id)');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955A76ED395');
        $this->addSql('DROP INDEX IDX_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE reservation RENAME COLUMN userx_id TO user_id');
        $this->addSql('COMMENT ON COLUMN reservation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('ALTER TABLE voiture DROP CONSTRAINT FK_E9E2810FA76ED395');
        $this->addSql('DROP INDEX IDX_E9E2810FA76ED395');
        $this->addSql('ALTER TABLE voiture RENAME COLUMN userx_id TO user_id');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FA76ED395 FOREIGN KEY (user_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E9E2810FA76ED395 ON voiture (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE preference_cdt DROP CONSTRAINT fk_13410deca76ed395');
        $this->addSql('DROP INDEX uniq_13410deca76ed395');
        $this->addSql('ALTER TABLE preference_cdt ADD userx_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE preference_cdt DROP user_id');
        $this->addSql('ALTER TABLE preference_cdt ADD CONSTRAINT fk_13410deca76ed395 FOREIGN KEY (userx_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_13410deca76ed395 ON preference_cdt (userx_id)');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c84955a76ed395');
        $this->addSql('DROP INDEX idx_42c84955a76ed395');
        $this->addSql('ALTER TABLE reservation ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE reservation RENAME COLUMN user_id TO userx_id');
        $this->addSql('COMMENT ON COLUMN reservation.created_at IS NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c84955a76ed395 FOREIGN KEY (userx_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_42c84955a76ed395 ON reservation (userx_id)');
        $this->addSql('ALTER TABLE covoiturage DROP CONSTRAINT fk_28c79e89a76ed395');
        $this->addSql('DROP INDEX idx_28c79e89a76ed395');
        $this->addSql('ALTER TABLE covoiturage RENAME COLUMN user_id TO userx_id');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT fk_28c79e89a76ed395 FOREIGN KEY (userx_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_28c79e89a76ed395 ON covoiturage (userx_id)');
        $this->addSql('ALTER TABLE compte DROP CONSTRAINT fk_cff65260a76ed395');
        $this->addSql('DROP INDEX uniq_cff65260a76ed395');
        $this->addSql('ALTER TABLE compte RENAME COLUMN user_id TO userx_id');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT fk_cff65260a76ed395 FOREIGN KEY (userx_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_cff65260a76ed395 ON compte (userx_id)');
        $this->addSql('ALTER TABLE voiture DROP CONSTRAINT fk_e9e2810fa76ed395');
        $this->addSql('DROP INDEX idx_e9e2810fa76ed395');
        $this->addSql('ALTER TABLE voiture RENAME COLUMN user_id TO userx_id');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT fk_e9e2810fa76ed395 FOREIGN KEY (userx_id) REFERENCES userx (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e9e2810fa76ed395 ON voiture (userx_id)');
    }
}

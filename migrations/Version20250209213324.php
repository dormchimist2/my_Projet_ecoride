<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209213324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E8972CCE3AC FOREIGN KEY (preference_cdt_id) REFERENCES preference_cdt (id)');
        $this->addSql('CREATE INDEX IDX_28C79E89A76ED395 ON covoiturage (user_id)');
        $this->addSql('CREATE INDEX IDX_28C79E89181A8BA ON covoiturage (voiture_id)');
        $this->addSql('CREATE INDEX IDX_28C79E8972CCE3AC ON covoiturage (preference_cdt_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89A76ED395');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89181A8BA');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E8972CCE3AC');
        $this->addSql('DROP INDEX IDX_28C79E89A76ED395 ON covoiturage');
        $this->addSql('DROP INDEX IDX_28C79E89181A8BA ON covoiturage');
        $this->addSql('DROP INDEX IDX_28C79E8972CCE3AC ON covoiturage');
    }
}

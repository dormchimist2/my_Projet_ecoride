<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221231850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Mettre à jour la table 'compte' en remplaçant user par userx
        $this->addSql('CREATE TABLE compte (id SERIAL NOT NULL, userx_id INT DEFAULT NULL, solde NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CFF65260A76ED395 ON compte (userx_id)');

        // Mettre à jour la table 'covoiturage'
        $this->addSql('CREATE TABLE covoiturage (id SERIAL NOT NULL, userx_id INT NOT NULL, voiture_id INT DEFAULT NULL, preference_cdt_id INT DEFAULT NULL, date_depart DATE NOT NULL, heure_depart TIME(0) WITHOUT TIME ZONE NOT NULL, point_depart VARCHAR(50) NOT NULL, date_arrivee DATE NOT NULL, heure_arrivee TIME(0) WITHOUT TIME ZONE NOT NULL, point_arrivee VARCHAR(50) NOT NULL, nb_place INT NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_28C79E89A76ED395 ON covoiturage (userx_id)');
        $this->addSql('CREATE INDEX IDX_28C79E89181A8BA ON covoiturage (voiture_id)');
        $this->addSql('CREATE INDEX IDX_28C79E8972CCE3AC ON covoiturage (preference_cdt_id)');

        // Mettre à jour la table 'preference_cdt'
        $this->addSql('CREATE TABLE preference_cdt (id SERIAL NOT NULL, userx_id INT DEFAULT NULL, accepte_fumeurs BOOLEAN NOT NULL, accepte_animaux_g BOOLEAN NOT NULL, accepte_animaux_p BOOLEAN NOT NULL, musique_autorisee BOOLEAN NOT NULL, discussion_autorisee BOOLEAN NOT NULL, siege_bebe BOOLEAN NOT NULL, ad_preference VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_13410DECA76ED395 ON preference_cdt (userx_id)');

        // Mettre à jour la table 'reservation'
        $this->addSql('CREATE TABLE reservation (id SERIAL NOT NULL, userx_id INT NOT NULL, covoiturage_id INT NOT NULL, nb_places INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(20) NOT NULL, total_prix NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (userx_id)');
        $this->addSql('CREATE INDEX IDX_42C8495562671590 ON reservation (covoiturage_id)');

        // Mettre à jour la table 'user' en 'userx'
        $this->addSql('CREATE TABLE "userx" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, phone_number VARCHAR(50) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(50) DEFAULT NULL, city VARCHAR(100) NOT NULL, country VARCHAR(100) NOT NULL, photo BYTEA DEFAULT NULL, pseudo VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "userx" (email)');

        // Mettre à jour la table 'voiture'
        $this->addSql('CREATE TABLE voiture (id SERIAL NOT NULL, userx_id INT DEFAULT NULL, marque VARCHAR(50) DEFAULT NULL, modele VARCHAR(100) DEFAULT NULL, couleur VARCHAR(50) DEFAULT NULL, date_matriculation DATE NOT NULL, n_immatriculation VARCHAR(50) NOT NULL, moteur VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9E2810FA76ED395 ON voiture (userx_id)');

        // Mettre à jour les contraintes
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260A76ED395 FOREIGN KEY (userx_id) REFERENCES "userx" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89A76ED395 FOREIGN KEY (userx_id) REFERENCES "userx" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E8972CCE3AC FOREIGN KEY (preference_cdt_id) REFERENCES preference_cdt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE preference_cdt ADD CONSTRAINT FK_13410DECA76ED395 FOREIGN KEY (userx_id) REFERENCES "userx" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (userx_id) REFERENCES "userx" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495562671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FA76ED395 FOREIGN KEY (userx_id) REFERENCES "userx" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // Annuler les modifications faites dans 'up'
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE compte DROP CONSTRAINT FK_CFF65260A76ED395');
        $this->addSql('ALTER TABLE covoiturage DROP CONSTRAINT FK_28C79E89A76ED395');
        $this->addSql('ALTER TABLE covoiturage DROP CONSTRAINT FK_28C79E89181A8BA');
        $this->addSql('ALTER TABLE covoiturage DROP CONSTRAINT FK_28C79E8972CCE3AC');
        $this->addSql('ALTER TABLE preference_cdt DROP CONSTRAINT FK_13410DECA76ED395');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C8495562671590');
        $this->addSql('ALTER TABLE voiture DROP CONSTRAINT FK_E9E2810FA76ED395');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE covoiturage');
        $this->addSql('DROP TABLE preference_cdt');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE "userx"'); // On supprime "userx" au lieu de "user"
        $this->addSql('DROP TABLE voiture');
    }
}

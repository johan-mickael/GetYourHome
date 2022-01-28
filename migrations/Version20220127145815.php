<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220127145815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateurs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, date_naissance DATE NOT NULL, adresse VARCHAR(255) NOT NULL, code_postale INT NOT NULL, ville VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_B5ED4E13A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(20) NOT NULL, prenom VARCHAR(20) NOT NULL, date_naissance DATE NOT NULL, adresse VARCHAR(100) NOT NULL, code_postale INT NOT NULL, ville VARCHAR(50) NOT NULL, telephone VARCHAR(15) NOT NULL, UNIQUE INDEX UNIQ_C82E74A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etapes (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projets (id INT AUTO_INCREMENT NOT NULL, client_id_id INT NOT NULL, etat_id INT NOT NULL, nom VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, INDEX IDX_B454C1DBDC2902E0 (client_id_id), INDEX IDX_B454C1DBD5E86FF (etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projets_etapes (projets_id INT NOT NULL, etapes_id INT NOT NULL, INDEX IDX_3684F3FC597A6CB7 (projets_id), INDEX IDX_3684F3FC4F5CEED2 (etapes_id), PRIMARY KEY(projets_id, etapes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projets_documents (projets_id INT NOT NULL, documents_id INT NOT NULL, INDEX IDX_E23726AF597A6CB7 (projets_id), INDEX IDX_E23726AF5F0F2752 (documents_id), PRIMARY KEY(projets_id, documents_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projets_etat (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrateurs ADD CONSTRAINT FK_B5ED4E13A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE clients ADD CONSTRAINT FK_C82E74A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DBDC2902E0 FOREIGN KEY (client_id_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DBD5E86FF FOREIGN KEY (etat_id) REFERENCES projets_etat (id)');
        $this->addSql('ALTER TABLE projets_etapes ADD CONSTRAINT FK_3684F3FC597A6CB7 FOREIGN KEY (projets_id) REFERENCES projets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projets_etapes ADD CONSTRAINT FK_3684F3FC4F5CEED2 FOREIGN KEY (etapes_id) REFERENCES etapes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projets_documents ADD CONSTRAINT FK_E23726AF597A6CB7 FOREIGN KEY (projets_id) REFERENCES projets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projets_documents ADD CONSTRAINT FK_E23726AF5F0F2752 FOREIGN KEY (documents_id) REFERENCES documents (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DBDC2902E0');
        $this->addSql('ALTER TABLE projets_documents DROP FOREIGN KEY FK_E23726AF5F0F2752');
        $this->addSql('ALTER TABLE projets_etapes DROP FOREIGN KEY FK_3684F3FC4F5CEED2');
        $this->addSql('ALTER TABLE projets_etapes DROP FOREIGN KEY FK_3684F3FC597A6CB7');
        $this->addSql('ALTER TABLE projets_documents DROP FOREIGN KEY FK_E23726AF597A6CB7');
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DBD5E86FF');
        $this->addSql('ALTER TABLE administrateurs DROP FOREIGN KEY FK_B5ED4E13A76ED395');
        $this->addSql('ALTER TABLE clients DROP FOREIGN KEY FK_C82E74A76ED395');
        $this->addSql('DROP TABLE administrateurs');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE etapes');
        $this->addSql('DROP TABLE projets');
        $this->addSql('DROP TABLE projets_etapes');
        $this->addSql('DROP TABLE projets_documents');
        $this->addSql('DROP TABLE projets_etat');
        $this->addSql('DROP TABLE user');
    }
}

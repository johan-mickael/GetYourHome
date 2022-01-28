<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220127220756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DBDC2902E0');
        $this->addSql('DROP INDEX IDX_B454C1DBDC2902E0 ON projets');
        $this->addSql('ALTER TABLE projets CHANGE client_id_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DB19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('CREATE INDEX IDX_B454C1DB19EB6921 ON projets (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DB19EB6921');
        $this->addSql('DROP INDEX IDX_B454C1DB19EB6921 ON projets');
        $this->addSql('ALTER TABLE projets CHANGE client_id client_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DBDC2902E0 FOREIGN KEY (client_id_id) REFERENCES clients (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B454C1DBDC2902E0 ON projets (client_id_id)');
    }
}

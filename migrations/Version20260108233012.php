<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260108233012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lease (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, initial_rent NUMERIC(10, 2) NOT NULL, current_rent NUMERIC(10, 2) NOT NULL, reference_index NUMERIC(10, 2) NOT NULL, last_revaluation_date DATETIME NOT NULL, tenant_id INT NOT NULL, INDEX IDX_E6C774959033212A (tenant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rent_revaluation_notification (id INT AUTO_INCREMENT NOT NULL, old_rent NUMERIC(10, 2) NOT NULL, new_rent NUMERIC(10, 2) NOT NULL, index_used NUMERIC(10, 2) NOT NULL, deposit_proof_url VARCHAR(255) DEFAULT NULL, receipt_proof_url VARCHAR(255) DEFAULT NULL, lease_id INT NOT NULL, INDEX IDX_60C5D248D3CA542C (lease_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, address1 VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE lease ADD CONSTRAINT FK_E6C774959033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE rent_revaluation_notification ADD CONSTRAINT FK_60C5D248D3CA542C FOREIGN KEY (lease_id) REFERENCES lease (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lease DROP FOREIGN KEY FK_E6C774959033212A');
        $this->addSql('ALTER TABLE rent_revaluation_notification DROP FOREIGN KEY FK_60C5D248D3CA542C');
        $this->addSql('DROP TABLE lease');
        $this->addSql('DROP TABLE rent_revaluation_notification');
        $this->addSql('DROP TABLE tenant');
    }
}

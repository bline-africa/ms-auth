<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211214103322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX uniq_81398e09f85e0677');
        $this->addSql('DROP INDEX uniq_81398e09e7927c74');
        $this->addSql('ALTER TABLE customer DROP email');
        $this->addSql('ALTER TABLE customer DROP roles');
        $this->addSql('ALTER TABLE customer DROP password');
        $this->addSql('ALTER TABLE customer DROP username');
        $this->addSql('ALTER TABLE customer DROP address');
        $this->addSql('ALTER TABLE customer DROP tel');
        $this->addSql('ALTER TABLE customer DROP tel2');
        $this->addSql('ALTER TABLE customer DROP firstname');
        $this->addSql('ALTER TABLE customer DROP lastname');
        $this->addSql('ALTER TABLE customer DROP avatar');
        $this->addSql('ALTER TABLE customer DROP isvalid');
        $this->addSql('ALTER TABLE customer DROP account_type');
        $this->addSql('ALTER TABLE customer DROP created_at');
        $this->addSql('ALTER TABLE customer DROP updated_at');
        $this->addSql('ALTER TABLE customer DROP updated_by');
        $this->addSql('ALTER TABLE customer DROP deleted');
        $this->addSql('ALTER TABLE customer DROP deleted_at');
        $this->addSql('ALTER TABLE provider RENAME COLUMN tel TO phone1');
        $this->addSql('ALTER TABLE provider RENAME COLUMN tel2 TO phone2');
        $this->addSql('ALTER TABLE "user" ADD profil_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN tel TO phone1');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN tel2 TO phone2');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64931484513 FOREIGN KEY (profil_id_id) REFERENCES profil_admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D64931484513 ON "user" (profil_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('ALTER TABLE customer ADD email VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE customer ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD username VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD tel VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD tel2 VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD firstname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD lastname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD avatar VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD isvalid BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE customer ADD account_type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE customer ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD deleted BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN customer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN customer.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN customer.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_81398e09f85e0677 ON customer (username)');
        $this->addSql('CREATE UNIQUE INDEX uniq_81398e09e7927c74 ON customer (email)');
        $this->addSql('ALTER TABLE provider RENAME COLUMN phone1 TO tel');
        $this->addSql('ALTER TABLE provider RENAME COLUMN phone2 TO tel2');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64931484513');
        $this->addSql('DROP INDEX IDX_8D93D64931484513');
        $this->addSql('ALTER TABLE "user" DROP profil_id_id');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN phone1 TO tel');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN phone2 TO tel2');
    }
}

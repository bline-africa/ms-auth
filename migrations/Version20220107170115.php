<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220107170115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admin (id UUID NOT NULL, profil_id_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, phone1 VARCHAR(50) NOT NULL, phone2 VARCHAR(50) DEFAULT NULL, is_valid BOOLEAN DEFAULT NULL, deleted_by INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, avatar VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, lastname VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76F85E0677 ON admin (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76E7927C74 ON admin (email)');
        $this->addSql('CREATE INDEX IDX_880E0D7631484513 ON admin (profil_id_id)');
        $this->addSql('COMMENT ON COLUMN admin.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN admin.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN admin.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE admin_meta (id INT NOT NULL, admin_id_id UUID DEFAULT NULL, key VARCHAR(255) NOT NULL, value JSON NOT NULL, code VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F7EB96D5DF6E65AD ON admin_meta (admin_id_id)');
        $this->addSql('COMMENT ON COLUMN admin_meta.admin_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN admin_meta.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN admin_meta.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN admin_meta.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE categorie_user_meta (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C57FBA4D60759 ON categorie_user_meta (libelle)');
        $this->addSql('CREATE TABLE category_meta (id INT NOT NULL, libelle VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, deleted BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE documents (id INT NOT NULL, user_id_id UUID NOT NULL, file_url VARCHAR(255) NOT NULL, is_valid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A2B072889D86650F ON documents (user_id_id)');
        $this->addSql('COMMENT ON COLUMN documents.user_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN documents.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN documents.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN documents.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE history (id INT NOT NULL, user_id_id UUID DEFAULT NULL, address_ip VARCHAR(25) NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude VARCHAR(25) DEFAULT NULL, date_connect TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27BA704B9D86650F ON history (user_id_id)');
        $this->addSql('COMMENT ON COLUMN history.user_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE profil_admin (id INT NOT NULL, libelle VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, deleted BOOLEAN DEFAULT NULL, is_valid BOOLEAN NOT NULL, roles JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C602D632A4D60759 ON profil_admin (libelle)');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, profil_id_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone1 VARCHAR(50) NOT NULL, phone2 VARCHAR(50) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, isvalid BOOLEAN NOT NULL, account_type VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted BOOLEAN DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_kyc_check BOOLEAN DEFAULT NULL, must_change_password BOOLEAN DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, account_id VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, title VARCHAR(10) DEFAULT NULL, last_connect TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tva DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE INDEX IDX_8D93D64931484513 ON "user" (profil_id_id)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_meta (id INT NOT NULL, user_id_id UUID DEFAULT NULL, categorie_id_id INT DEFAULT NULL, key VARCHAR(255) NOT NULL, value JSON NOT NULL, code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delete_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted BOOLEAN NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AD7358FC9D86650F ON user_meta (user_id_id)');
        $this->addSql('CREATE INDEX IDX_AD7358FC8A3C7387 ON user_meta (categorie_id_id)');
        $this->addSql('COMMENT ON COLUMN user_meta.user_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_meta.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_meta.delete_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_meta.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D7631484513 FOREIGN KEY (profil_id_id) REFERENCES profil_admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admin_meta ADD CONSTRAINT FK_F7EB96D5DF6E65AD FOREIGN KEY (admin_id_id) REFERENCES admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B072889D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64931484513 FOREIGN KEY (profil_id_id) REFERENCES profil_admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_meta ADD CONSTRAINT FK_AD7358FC9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_meta ADD CONSTRAINT FK_AD7358FC8A3C7387 FOREIGN KEY (categorie_id_id) REFERENCES categorie_user_meta (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE admin_meta DROP CONSTRAINT FK_F7EB96D5DF6E65AD');
        $this->addSql('ALTER TABLE user_meta DROP CONSTRAINT FK_AD7358FC8A3C7387');
        $this->addSql('ALTER TABLE admin DROP CONSTRAINT FK_880E0D7631484513');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64931484513');
        $this->addSql('ALTER TABLE documents DROP CONSTRAINT FK_A2B072889D86650F');
        $this->addSql('ALTER TABLE history DROP CONSTRAINT FK_27BA704B9D86650F');
        $this->addSql('ALTER TABLE user_meta DROP CONSTRAINT FK_AD7358FC9D86650F');
        $this->addSql('DROP SEQUENCE history_id_seq CASCADE');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE admin_meta');
        $this->addSql('DROP TABLE categorie_user_meta');
        $this->addSql('DROP TABLE category_meta');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE profil_admin');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_meta');
    }
}

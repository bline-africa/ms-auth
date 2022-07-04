<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704161247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE delete_requests_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE delete_requests (id INT NOT NULL, user_name VARCHAR(255) NOT NULL, user_id UUID NOT NULL, date_request TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_done BOOLEAN DEFAULT NULL, motif TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN delete_requests.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD is_deleted BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD data_deleted TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE delete_requests_id_seq CASCADE');
        $this->addSql('DROP TABLE delete_requests');
        $this->addSql('ALTER TABLE "user" DROP is_deleted');
        $this->addSql('ALTER TABLE "user" DROP data_deleted');
    }
}

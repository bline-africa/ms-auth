<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211215105927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD is_kyc_check BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE provider ADD must_change_password BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD is_kyc_check BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD must_change_password BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE provider DROP is_kyc_check');
        $this->addSql('ALTER TABLE provider DROP must_change_password');
        $this->addSql('ALTER TABLE "user" DROP is_kyc_check');
        $this->addSql('ALTER TABLE "user" DROP must_change_password');
    }
}

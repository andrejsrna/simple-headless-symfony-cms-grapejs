<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241223082503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings ADD storage_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD s3_endpoint VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD s3_region VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD s3_bucket VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD s3_access_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD s3_secret_key VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE settings DROP storage_type');
        $this->addSql('ALTER TABLE settings DROP s3_endpoint');
        $this->addSql('ALTER TABLE settings DROP s3_region');
        $this->addSql('ALTER TABLE settings DROP s3_bucket');
        $this->addSql('ALTER TABLE settings DROP s3_access_key');
        $this->addSql('ALTER TABLE settings DROP s3_secret_key');
    }
}

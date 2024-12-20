<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219223502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add createdAt field to Article entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE article ALTER created_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN article.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP created_at');
    }
}

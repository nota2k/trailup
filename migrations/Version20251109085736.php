<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109085736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE itineraires DROP INDEX UNIQ_71F6936373A201E5, ADD INDEX IDX_71F6936373A201E5 (createur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE itineraires DROP INDEX IDX_71F6936373A201E5, ADD UNIQUE INDEX UNIQ_71F6936373A201E5 (createur_id)');
    }
}

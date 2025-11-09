<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109082634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE info_user DROP FOREIGN KEY FK_D4F804C7FB88E14F');
        $this->addSql('DROP INDEX FK_D4F804C7FB88E14F ON info_user');
        $this->addSql('ALTER TABLE info_user CHANGE utilisateur_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE info_user ADD CONSTRAINT FK_D4F804C7A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4F804C7A76ED395 ON info_user (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE info_user DROP FOREIGN KEY FK_D4F804C7A76ED395');
        $this->addSql('DROP INDEX UNIQ_D4F804C7A76ED395 ON info_user');
        $this->addSql('ALTER TABLE info_user CHANGE user_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE info_user ADD CONSTRAINT FK_D4F804C7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX FK_D4F804C7FB88E14F ON info_user (utilisateur_id)');
    }
}

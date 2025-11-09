<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809182639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $connection = $this->connection;
        
        // Try to drop foreign keys, ignore if they don't exist
        try {
            $connection->executeStatement('ALTER TABLE chevaux DROP FOREIGN KEY FK_9990D6E676C50E4A');
        } catch (\Exception $e) {
            // Ignore if foreign key doesn't exist
        }
        
        try {
            $connection->executeStatement('ALTER TABLE info_user DROP FOREIGN KEY FK_D4F804C7A76ED395');
        } catch (\Exception $e) {
            // Ignore if foreign key doesn't exist
        }
        
        $this->addSql('CREATE TABLE IF NOT EXISTS itineraires_utilisateur (itineraires_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_A612D3A822623EC8 (itineraires_id), INDEX IDX_A612D3A8FB88E14F (utilisateur_id), PRIMARY KEY(itineraires_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS utilisateur (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Add constraints only if they don't exist
        try {
            $connection->executeStatement('ALTER TABLE itineraires_utilisateur ADD CONSTRAINT FK_A612D3A822623EC8 FOREIGN KEY (itineraires_id) REFERENCES itineraires (id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE itineraires_utilisateur ADD CONSTRAINT FK_A612D3A8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE itineraires_user DROP FOREIGN KEY FK_26BCA25022623EC8');
            $connection->executeStatement('ALTER TABLE itineraires_user DROP FOREIGN KEY FK_26BCA250A76ED395');
            $connection->executeStatement('DROP TABLE itineraires_user');
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }
        
        try {
            $connection->executeStatement('DROP TABLE user');
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }
        
        try {
            $connection->executeStatement('ALTER TABLE chevaux DROP FOREIGN KEY FK_9990D6E676C50E4A');
        } catch (\Exception $e) {
            // Ignore if foreign key doesn't exist
        }
        
        try {
            $connection->executeStatement('ALTER TABLE chevaux ADD CONSTRAINT FK_9990D6E676C50E4A FOREIGN KEY (proprietaire_id) REFERENCES utilisateur (id)');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('DROP INDEX UNIQ_D4F804C7A76ED395 ON info_user');
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }
        
        $this->addSql('ALTER TABLE info_user CHANGE user_id utilisateur_id INT DEFAULT NULL');
        
        try {
            $connection->executeStatement('ALTER TABLE info_user ADD CONSTRAINT FK_D4F804C7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('CREATE UNIQUE INDEX UNIQ_D4F804C7FB88E14F ON info_user (utilisateur_id)');
        } catch (\Exception $e) {
            // Ignore if index already exists
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chevaux DROP FOREIGN KEY FK_9990D6E676C50E4A');
        $this->addSql('ALTER TABLE info_user DROP FOREIGN KEY FK_D4F804C7FB88E14F');
        $this->addSql('CREATE TABLE itineraires_user (itineraires_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_26BCA25022623EC8 (itineraires_id), INDEX IDX_26BCA250A76ED395 (user_id), PRIMARY KEY(itineraires_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE itineraires_user ADD CONSTRAINT FK_26BCA25022623EC8 FOREIGN KEY (itineraires_id) REFERENCES itineraires (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE itineraires_user ADD CONSTRAINT FK_26BCA250A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE itineraires_utilisateur DROP FOREIGN KEY FK_A612D3A822623EC8');
        $this->addSql('ALTER TABLE itineraires_utilisateur DROP FOREIGN KEY FK_A612D3A8FB88E14F');
        $this->addSql('DROP TABLE itineraires_utilisateur');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('ALTER TABLE chevaux DROP FOREIGN KEY FK_9990D6E676C50E4A');
        $this->addSql('ALTER TABLE chevaux ADD CONSTRAINT FK_9990D6E676C50E4A FOREIGN KEY (proprietaire_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX UNIQ_D4F804C7FB88E14F ON info_user');
        $this->addSql('ALTER TABLE info_user CHANGE utilisateur_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE info_user ADD CONSTRAINT FK_D4F804C7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4F804C7A76ED395 ON info_user (user_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109082401 extends AbstractMigration
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
        
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE IF NOT EXISTS discussions (id INT AUTO_INCREMENT NOT NULL, user1_id INT NOT NULL, user2_id INT NOT NULL, sujet VARCHAR(255) NOT NULL, INDEX IDX_8B716B6356AE248B (user1_id), INDEX IDX_8B716B63441B8B65 (user2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT NOT NULL, discussion_id INT DEFAULT NULL, expediteur_id INT DEFAULT NULL, date DATE NOT NULL, heure TIME NOT NULL, lu TINYINT(1) NOT NULL, prio TINYINT(1) NOT NULL, body LONGTEXT NOT NULL, INDEX IDX_DB021E961ADED311 (discussion_id), INDEX IDX_DB021E9610335F61 (expediteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS utilisateur_itineraires (utilisateur_id INT NOT NULL, itineraires_id INT NOT NULL, INDEX IDX_85109EF0FB88E14F (utilisateur_id), INDEX IDX_85109EF022623EC8 (itineraires_id), PRIMARY KEY(utilisateur_id, itineraires_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Add constraints only if they don't exist
        try {
            $connection->executeStatement('ALTER TABLE discussions ADD CONSTRAINT FK_8B716B6356AE248B FOREIGN KEY (user1_id) REFERENCES utilisateur (id)');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE discussions ADD CONSTRAINT FK_8B716B63441B8B65 FOREIGN KEY (user2_id) REFERENCES utilisateur (id)');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE messages ADD CONSTRAINT FK_DB021E961ADED311 FOREIGN KEY (discussion_id) REFERENCES discussions (id)');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9610335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE utilisateur_itineraires ADD CONSTRAINT FK_85109EF0FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE utilisateur_itineraires ADD CONSTRAINT FK_85109EF022623EC8 FOREIGN KEY (itineraires_id) REFERENCES itineraires (id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        try {
            $connection->executeStatement('ALTER TABLE info_itineraire DROP FOREIGN KEY FK_10B9A6BDA9B853B8');
            $connection->executeStatement('DROP TABLE info_itineraire');
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }
        
        $this->addSql('ALTER TABLE chevaux CHANGE proprietaire_id proprietaire_id INT NOT NULL');
        
        try {
            $connection->executeStatement('ALTER TABLE chevaux ADD CONSTRAINT FK_9990D6E676C50E4A FOREIGN KEY (proprietaire_id) REFERENCES utilisateur (id)');
        } catch (\Exception $e) {
            // Ignore if constraint already exists
        }
        
        // Check if user_id column exists, if not use utilisateur_id
        $userColumns = $connection->fetchFirstColumn("SHOW COLUMNS FROM info_user LIKE 'user_id'");
        $utilisateurColumns = $connection->fetchFirstColumn("SHOW COLUMNS FROM info_user LIKE 'utilisateur_id'");
        
        if (!empty($userColumns)) {
            // Column is still user_id, modify it
            $this->addSql('ALTER TABLE info_user DROP age, CHANGE user_id user_id INT NOT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE ville ville VARCHAR(255) DEFAULT NULL, CHANGE region region VARCHAR(255) DEFAULT NULL');
            try {
                $connection->executeStatement('ALTER TABLE info_user ADD CONSTRAINT FK_D4F804C7A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
            } catch (\Exception $e) {
                // Ignore if constraint already exists
            }
        } elseif (!empty($utilisateurColumns)) {
            // Column is already utilisateur_id, just modify other columns
            try {
                $connection->executeStatement('ALTER TABLE info_user DROP age');
            } catch (\Exception $e) {
                // Ignore if column doesn't exist
            }
            $this->addSql('ALTER TABLE info_user CHANGE utilisateur_id utilisateur_id INT NOT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE ville ville VARCHAR(255) DEFAULT NULL, CHANGE region region VARCHAR(255) DEFAULT NULL');
            try {
                $connection->executeStatement('ALTER TABLE info_user ADD CONSTRAINT FK_D4F804C7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
            } catch (\Exception $e) {
                // Ignore if constraint already exists
            }
        }
        
        // Add columns to itineraires table
        $columnsToAdd = [
            'titre' => "ADD titre VARCHAR(150) NOT NULL DEFAULT ''",
            'niveau' => "ADD niveau LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)'",
            'allures' => "ADD allures LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)'",
            'depart' => "ADD depart VARCHAR(255) NOT NULL DEFAULT ''",
            'distance' => "ADD distance INT NOT NULL DEFAULT 0",
            'duree' => "ADD duree INT NOT NULL DEFAULT 0",
            'accepte' => "ADD accepte LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)'",
            'description' => "ADD description LONGTEXT NOT NULL DEFAULT ''",
            'validation' => "ADD validation TINYINT(1) NOT NULL DEFAULT 0",
            'createur_id' => "ADD createur_id INT NOT NULL DEFAULT 0"
        ];
        
        foreach ($columnsToAdd as $columnName => $sql) {
            try {
                $connection->executeStatement("ALTER TABLE itineraires $sql");
            } catch (\Exception $e) {
                // Column might already exist, ignore
            }
        }
        
        try {
            $connection->executeStatement('ALTER TABLE itineraires CHANGE publie publie TINYINT(1) NOT NULL');
        } catch (\Exception $e) {
            // Ignore
        }
        
        try {
            $connection->executeStatement('ALTER TABLE itineraires ADD CONSTRAINT FK_71F6936373A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateur (id)');
        } catch (\Exception $e) {
            // Constraint might already exist
        }
        
        try {
            $connection->executeStatement('CREATE UNIQUE INDEX UNIQ_71F6936373A201E5 ON itineraires (createur_id)');
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE info_itineraire (id INT AUTO_INCREMENT NOT NULL, itineraire_id INT DEFAULT NULL, niveau VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, allures VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, depart VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, distance INT NOT NULL, duree INT NOT NULL, accepte LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\', description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_10B9A6BDA9B853B8 (itineraire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE info_itineraire ADD CONSTRAINT FK_10B9A6BDA9B853B8 FOREIGN KEY (itineraire_id) REFERENCES itineraires (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE discussions DROP FOREIGN KEY FK_8B716B6356AE248B');
        $this->addSql('ALTER TABLE discussions DROP FOREIGN KEY FK_8B716B63441B8B65');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E961ADED311');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E9610335F61');
        $this->addSql('ALTER TABLE utilisateur_itineraires DROP FOREIGN KEY FK_85109EF0FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_itineraires DROP FOREIGN KEY FK_85109EF022623EC8');
        $this->addSql('DROP TABLE discussions');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE utilisateur_itineraires');
        $this->addSql('ALTER TABLE chevaux DROP FOREIGN KEY FK_9990D6E676C50E4A');
        $this->addSql('ALTER TABLE chevaux CHANGE proprietaire_id proprietaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE itineraires DROP FOREIGN KEY FK_71F6936373A201E5');
        $this->addSql('DROP INDEX UNIQ_71F6936373A201E5 ON itineraires');
        $this->addSql('ALTER TABLE itineraires DROP createur_id, DROP titre, DROP niveau, DROP allures, DROP depart, DROP distance, DROP duree, DROP accepte, DROP description, DROP validation, CHANGE publie publie VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE info_user DROP FOREIGN KEY FK_D4F804C7A76ED395');
        $this->addSql('ALTER TABLE info_user ADD age DATE NOT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE ville ville VARCHAR(255) NOT NULL, CHANGE region region VARCHAR(255) NOT NULL');
    }
}

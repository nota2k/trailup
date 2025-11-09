<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109082747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing columns to itineraires table including titre';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $connection = $this->connection;
        
        // Add columns to itineraires table if they don't exist
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
        $this->addSql('ALTER TABLE itineraires DROP FOREIGN KEY FK_71F6936373A201E5');
        $this->addSql('DROP INDEX UNIQ_71F6936373A201E5 ON itineraires');
        $this->addSql('ALTER TABLE itineraires DROP createur_id, DROP titre, DROP niveau, DROP allures, DROP depart, DROP distance, DROP duree, DROP accepte, DROP description, DROP validation');
    }
}

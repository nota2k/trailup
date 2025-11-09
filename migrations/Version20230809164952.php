<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809164952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chevaux (id INT AUTO_INCREMENT NOT NULL, proprietaire_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, race VARCHAR(255) NOT NULL, age DATE DEFAULT NULL, photo01 VARCHAR(255) DEFAULT NULL, photo02 VARCHAR(255) DEFAULT NULL, photo03 VARCHAR(255) DEFAULT NULL, sexe VARCHAR(255) NOT NULL, INDEX IDX_9990D6E676C50E4A (proprietaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_itineraire (id INT AUTO_INCREMENT NOT NULL, itineraire_id INT DEFAULT NULL, niveau VARCHAR(50) NOT NULL, allures VARCHAR(20) NOT NULL, depart VARCHAR(255) NOT NULL, distance INT NOT NULL, duree INT NOT NULL, accepte LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_10B9A6BDA9B853B8 (itineraire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, age DATE NOT NULL, ville VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, miniature VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D4F804C7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE itineraires (id INT AUTO_INCREMENT NOT NULL, publie VARBINARY(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE itineraires_user (itineraires_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_26BCA25022623EC8 (itineraires_id), INDEX IDX_26BCA250A76ED395 (user_id), PRIMARY KEY(itineraires_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chevaux ADD CONSTRAINT FK_9990D6E676C50E4A FOREIGN KEY (proprietaire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE info_itineraire ADD CONSTRAINT FK_10B9A6BDA9B853B8 FOREIGN KEY (itineraire_id) REFERENCES itineraires (id)');
        $this->addSql('ALTER TABLE info_user ADD CONSTRAINT FK_D4F804C7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE itineraires_user ADD CONSTRAINT FK_26BCA25022623EC8 FOREIGN KEY (itineraires_id) REFERENCES itineraires (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE itineraires_user ADD CONSTRAINT FK_26BCA250A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chevaux DROP FOREIGN KEY FK_9990D6E676C50E4A');
        $this->addSql('ALTER TABLE info_itineraire DROP FOREIGN KEY FK_10B9A6BDA9B853B8');
        $this->addSql('ALTER TABLE info_user DROP FOREIGN KEY FK_D4F804C7A76ED395');
        $this->addSql('ALTER TABLE itineraires_user DROP FOREIGN KEY FK_26BCA25022623EC8');
        $this->addSql('ALTER TABLE itineraires_user DROP FOREIGN KEY FK_26BCA250A76ED395');
        $this->addSql('DROP TABLE chevaux');
        $this->addSql('DROP TABLE info_itineraire');
        $this->addSql('DROP TABLE info_user');
        $this->addSql('DROP TABLE itineraires');
        $this->addSql('DROP TABLE itineraires_user');
        $this->addSql('DROP TABLE user');
    }
}

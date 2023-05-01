<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230501154437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team_color (id INT AUTO_INCREMENT NOT NULL, team_a VARCHAR(255) NOT NULL, team_b VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events ADD fk_teamcolor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AD0368DE6 FOREIGN KEY (fk_teamcolor_id) REFERENCES team_color (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5387574AD0368DE6 ON events (fk_teamcolor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AD0368DE6');
        $this->addSql('DROP TABLE team_color');
        $this->addSql('DROP INDEX UNIQ_5387574AD0368DE6 ON events');
        $this->addSql('ALTER TABLE events DROP fk_teamcolor_id');
    }
}

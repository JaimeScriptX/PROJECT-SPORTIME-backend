<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419172940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, fk_sport_id INT NOT NULL, fk_sport_center_id INT NOT NULL, name VARCHAR(255) NOT NULL, is_private TINYINT(1) NOT NULL, details VARCHAR(512) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, date DATE NOT NULL, time TIME NOT NULL, duration TIME NOT NULL, number_players INT NOT NULL, INDEX IDX_5387574A9E50FF26 (fk_sport_id), INDEX IDX_5387574AAB135F2F (fk_sport_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A9E50FF26 FOREIGN KEY (fk_sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AAB135F2F FOREIGN KEY (fk_sport_center_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE person ADD events_id INT NOT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1769D6A1065 FOREIGN KEY (events_id) REFERENCES events (id)');
        $this->addSql('CREATE INDEX IDX_34DCD1769D6A1065 ON person (events_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1769D6A1065');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A9E50FF26');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AAB135F2F');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP INDEX IDX_34DCD1769D6A1065 ON person');
        $this->addSql('ALTER TABLE person DROP events_id');
    }
}

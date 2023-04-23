<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421192737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A9E50FF26');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A9E50FF26 FOREIGN KEY (fk_sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE person ADD events_id INT NOT NULL, CHANGE fk_sex_id fk_sex_id INT NOT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1769D6A1065 FOREIGN KEY (events_id) REFERENCES events (id)');
        $this->addSql('CREATE INDEX IDX_34DCD1769D6A1065 ON person (events_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A9E50FF26');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A9E50FF26 FOREIGN KEY (fk_sport_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1769D6A1065');
        $this->addSql('DROP INDEX IDX_34DCD1769D6A1065 ON person');
        $this->addSql('ALTER TABLE person DROP events_id, CHANGE fk_sex_id fk_sex_id INT DEFAULT NULL');
    }
}

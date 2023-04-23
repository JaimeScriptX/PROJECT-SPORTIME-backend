<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421193824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events ADD fk_person_id INT NOT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A40226CD7 FOREIGN KEY (fk_person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_5387574A40226CD7 ON events (fk_person_id)');
        $this->addSql('ALTER TABLE person DROP events_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A40226CD7');
        $this->addSql('DROP INDEX IDX_5387574A40226CD7 ON events');
        $this->addSql('ALTER TABLE events DROP fk_person_id');
        $this->addSql('ALTER TABLE person ADD events_id INT NOT NULL');
    }
}

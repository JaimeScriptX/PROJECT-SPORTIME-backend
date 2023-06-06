<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606081111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events_results DROP FOREIGN KEY FK_C9C29D0EFC33C7F5');
        $this->addSql('DROP INDEX IDX_C9C29D0EFC33C7F5 ON events_results');
        $this->addSql('ALTER TABLE events_results DROP fk_events_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events_results ADD fk_events_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events_results ADD CONSTRAINT FK_C9C29D0EFC33C7F5 FOREIGN KEY (fk_events_id) REFERENCES events (id)');
        $this->addSql('CREATE INDEX IDX_C9C29D0EFC33C7F5 ON events_results (fk_events_id)');
    }
}

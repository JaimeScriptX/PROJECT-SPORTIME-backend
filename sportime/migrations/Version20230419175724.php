<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419175724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE events_results (id INT AUTO_INCREMENT NOT NULL, fk_events_id INT NOT NULL, team_a INT NOT NULL, team_b INT NOT NULL, UNIQUE INDEX UNIQ_C9C29D0EFC33C7F5 (fk_events_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events_results ADD CONSTRAINT FK_C9C29D0EFC33C7F5 FOREIGN KEY (fk_events_id) REFERENCES events (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events_results DROP FOREIGN KEY FK_C9C29D0EFC33C7F5');
        $this->addSql('DROP TABLE events_results');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230501152943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A7F370AF5');
        $this->addSql('DROP INDEX IDX_5387574A7F370AF5 ON events');
        $this->addSql('ALTER TABLE events DROP fk_team_colours_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events ADD fk_team_colours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A7F370AF5 FOREIGN KEY (fk_team_colours_id) REFERENCES events (id)');
        $this->addSql('CREATE INDEX IDX_5387574A7F370AF5 ON events (fk_team_colours_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419180454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events ADD fk_difficulty_id INT NOT NULL, ADD fk_team_colours_id INT NOT NULL, ADD fk_sex_id INT NOT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A98B0E86C FOREIGN KEY (fk_difficulty_id) REFERENCES difficulty (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A7F370AF5 FOREIGN KEY (fk_team_colours_id) REFERENCES team_color (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A7A83DA30 FOREIGN KEY (fk_sex_id) REFERENCES sex (id)');
        $this->addSql('CREATE INDEX IDX_5387574A98B0E86C ON events (fk_difficulty_id)');
        $this->addSql('CREATE INDEX IDX_5387574A7F370AF5 ON events (fk_team_colours_id)');
        $this->addSql('CREATE INDEX IDX_5387574A7A83DA30 ON events (fk_sex_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A98B0E86C');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A7F370AF5');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A7A83DA30');
        $this->addSql('DROP INDEX IDX_5387574A98B0E86C ON events');
        $this->addSql('DROP INDEX IDX_5387574A7F370AF5 ON events');
        $this->addSql('DROP INDEX IDX_5387574A7A83DA30 ON events');
        $this->addSql('ALTER TABLE events DROP fk_difficulty_id, DROP fk_team_colours_id, DROP fk_sex_id');
    }
}

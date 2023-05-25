<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525191055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events ADD fk_teamcolor_two_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A563F0DEA FOREIGN KEY (fk_teamcolor_two_id) REFERENCES team_color (id)');
        $this->addSql('CREATE INDEX IDX_5387574A563F0DEA ON events (fk_teamcolor_two_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A563F0DEA');
        $this->addSql('DROP INDEX IDX_5387574A563F0DEA ON events');
        $this->addSql('ALTER TABLE events DROP fk_teamcolor_two_id');
    }
}

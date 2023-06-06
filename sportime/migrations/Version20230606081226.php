<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606081226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events ADD fk_results_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AF8CDC02A FOREIGN KEY (fk_results_id) REFERENCES events_results (id)');
        $this->addSql('CREATE INDEX IDX_5387574AF8CDC02A ON events (fk_results_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AF8CDC02A');
        $this->addSql('DROP INDEX IDX_5387574AF8CDC02A ON events');
        $this->addSql('ALTER TABLE events DROP fk_results_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525190443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_color ADD colour VARCHAR(255) NOT NULL, ADD image_shirt VARCHAR(255) NOT NULL, DROP team_a, DROP team_b');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_color ADD team_a VARCHAR(255) NOT NULL, ADD team_b VARCHAR(255) NOT NULL, DROP colour, DROP image_shirt');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419180541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person ADD fk_sex_id INT NOT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1767A83DA30 FOREIGN KEY (fk_sex_id) REFERENCES sex (id)');
        $this->addSql('CREATE INDEX IDX_34DCD1767A83DA30 ON person (fk_sex_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1767A83DA30');
        $this->addSql('DROP INDEX IDX_34DCD1767A83DA30 ON person');
        $this->addSql('ALTER TABLE person DROP fk_sex_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230510144801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person ADD fk_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1765741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD1765741EEB9 ON person (fk_user_id)');
        $this->addSql('ALTER TABLE user DROP image_profile');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1765741EEB9');
        $this->addSql('DROP INDEX UNIQ_34DCD1765741EEB9 ON person');
        $this->addSql('ALTER TABLE person DROP fk_user_id');
        $this->addSql('ALTER TABLE user ADD image_profile VARCHAR(255) DEFAULT NULL');
    }
}

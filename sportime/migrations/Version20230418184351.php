<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418184351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sport_center (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, services VARCHAR(255) DEFAULT NULL, image VARCHAR(512) DEFAULT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_center_sport (sport_center_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_3AA4F82672B00B1A (sport_center_id), INDEX IDX_3AA4F826AC78BCF8 (sport_id), PRIMARY KEY(sport_center_id, sport_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sport_center_sport ADD CONSTRAINT FK_3AA4F82672B00B1A FOREIGN KEY (sport_center_id) REFERENCES sport_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sport_center_sport ADD CONSTRAINT FK_3AA4F826AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sport_center_sport DROP FOREIGN KEY FK_3AA4F82672B00B1A');
        $this->addSql('ALTER TABLE sport_center_sport DROP FOREIGN KEY FK_3AA4F826AC78BCF8');
        $this->addSql('DROP TABLE sport_center');
        $this->addSql('DROP TABLE sport_center_sport');
    }
}

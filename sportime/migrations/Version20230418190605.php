<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418190605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location_sport_center (id INT AUTO_INCREMENT NOT NULL, fk_sport_center_id INT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, destination VARCHAR(255) NOT NULL, INDEX IDX_55886FEBAB135F2F (fk_sport_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable (id INT AUTO_INCREMENT NOT NULL, fk_sportcenter_id INT DEFAULT NULL, dia VARCHAR(255) NOT NULL, open TIME NOT NULL, close TIME NOT NULL, day VARCHAR(255) NOT NULL, opening TIME NOT NULL, closing TIME NOT NULL, INDEX IDX_6B1F670345EA044 (fk_sportcenter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location_sport_center ADD CONSTRAINT FK_55886FEBAB135F2F FOREIGN KEY (fk_sport_center_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F670345EA044 FOREIGN KEY (fk_sportcenter_id) REFERENCES sport_center (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location_sport_center DROP FOREIGN KEY FK_55886FEBAB135F2F');
        $this->addSql('ALTER TABLE timetable DROP FOREIGN KEY FK_6B1F670345EA044');
        $this->addSql('DROP TABLE location_sport_center');
        $this->addSql('DROP TABLE timetable');
    }
}

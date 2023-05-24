<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522183708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location_sport_center DROP FOREIGN KEY FK_55886FEBAB135F2F');
        $this->addSql('DROP TABLE location_sport_center');
        $this->addSql('ALTER TABLE sport ADD logo_sportcenter VARCHAR(512) DEFAULT NULL, CHANGE logo logo_event VARCHAR(512) DEFAULT NULL');
        $this->addSql('ALTER TABLE sport_center ADD image_gallery1 VARCHAR(512) DEFAULT NULL, ADD image_gallery2 VARCHAR(512) DEFAULT NULL, ADD image_gallery3 VARCHAR(512) DEFAULT NULL, ADD image_gallery4 VARCHAR(512) DEFAULT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, ADD destination VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location_sport_center (id INT AUTO_INCREMENT NOT NULL, fk_sport_center_id INT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, destination VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_55886FEBAB135F2F (fk_sport_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE location_sport_center ADD CONSTRAINT FK_55886FEBAB135F2F FOREIGN KEY (fk_sport_center_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE sport ADD logo VARCHAR(512) DEFAULT NULL, DROP logo_event, DROP logo_sportcenter');
        $this->addSql('ALTER TABLE sport_center DROP image_gallery1, DROP image_gallery2, DROP image_gallery3, DROP image_gallery4, DROP latitude, DROP longitude, DROP destination');
    }
}

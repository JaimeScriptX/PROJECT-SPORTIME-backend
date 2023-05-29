<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230529193419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reserved_time (id INT AUTO_INCREMENT NOT NULL, fk_sport_center_id_id INT DEFAULT NULL, fk_event_id_id INT DEFAULT NULL, day INT NOT NULL, date DATE NOT NULL, start TIME NOT NULL, end TIME NOT NULL, date_created DATE NOT NULL, canceled TINYINT(1) NOT NULL, cancellation_reason VARCHAR(255) DEFAULT NULL, INDEX IDX_3F312D9AECCDC403 (fk_sport_center_id_id), INDEX IDX_3F312D9A57A9834F (fk_event_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule_center (id INT AUTO_INCREMENT NOT NULL, fk_sport_center_id_id INT DEFAULT NULL, day INT NOT NULL, start TIME NOT NULL, end TIME NOT NULL, INDEX IDX_22D1AF3BECCDC403 (fk_sport_center_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reserved_time ADD CONSTRAINT FK_3F312D9AECCDC403 FOREIGN KEY (fk_sport_center_id_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE reserved_time ADD CONSTRAINT FK_3F312D9A57A9834F FOREIGN KEY (fk_event_id_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE schedule_center ADD CONSTRAINT FK_22D1AF3BECCDC403 FOREIGN KEY (fk_sport_center_id_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE timetable DROP FOREIGN KEY FK_6B1F670345EA044');
        $this->addSql('DROP TABLE timetable');
        $this->addSql('ALTER TABLE events ADD fk_state_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A37F07F1F FOREIGN KEY (fk_state_id) REFERENCES state (id)');
        $this->addSql('CREATE INDEX IDX_5387574A37F07F1F ON events (fk_state_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE timetable (id INT AUTO_INCREMENT NOT NULL, fk_sportcenter_id INT DEFAULT NULL, open TIME NOT NULL, close TIME NOT NULL, day VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_6B1F670345EA044 (fk_sportcenter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F670345EA044 FOREIGN KEY (fk_sportcenter_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE reserved_time DROP FOREIGN KEY FK_3F312D9AECCDC403');
        $this->addSql('ALTER TABLE reserved_time DROP FOREIGN KEY FK_3F312D9A57A9834F');
        $this->addSql('ALTER TABLE schedule_center DROP FOREIGN KEY FK_22D1AF3BECCDC403');
        $this->addSql('DROP TABLE reserved_time');
        $this->addSql('DROP TABLE schedule_center');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A37F07F1F');
        $this->addSql('DROP INDEX IDX_5387574A37F07F1F ON events');
        $this->addSql('ALTER TABLE events DROP fk_state_id');
    }
}

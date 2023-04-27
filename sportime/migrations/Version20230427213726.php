<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230427213726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE difficulty (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_players (id INT AUTO_INCREMENT NOT NULL, fk_event_id INT DEFAULT NULL, fk_person_id INT DEFAULT NULL, INDEX IDX_A319EA0943DFAB55 (fk_event_id), INDEX IDX_A319EA0940226CD7 (fk_person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, fk_sport_id INT DEFAULT NULL, fk_sportcenter_id INT DEFAULT NULL, fk_difficulty_id INT DEFAULT NULL, fk_sex_id INT DEFAULT NULL, fk_person_id INT DEFAULT NULL, fk_team_colours_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_private TINYINT(1) NOT NULL, details VARCHAR(512) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, date DATE NOT NULL, time TIME NOT NULL, duration TIME NOT NULL, number_players INT NOT NULL, INDEX IDX_5387574A9E50FF26 (fk_sport_id), INDEX IDX_5387574A345EA044 (fk_sportcenter_id), INDEX IDX_5387574A98B0E86C (fk_difficulty_id), INDEX IDX_5387574A7A83DA30 (fk_sex_id), INDEX IDX_5387574A40226CD7 (fk_person_id), INDEX IDX_5387574A7F370AF5 (fk_team_colours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_results (id INT AUTO_INCREMENT NOT NULL, fk_events_id INT DEFAULT NULL, team_a INT NOT NULL, team_b INT NOT NULL, INDEX IDX_C9C29D0EFC33C7F5 (fk_events_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location_sport_center (id INT AUTO_INCREMENT NOT NULL, fk_sport_center_id INT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, destination VARCHAR(255) NOT NULL, INDEX IDX_55886FEBAB135F2F (fk_sport_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, fk_sex_id INT DEFAULT NULL, fk_user_id INT DEFAULT NULL, image_profile VARCHAR(512) DEFAULT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birthday DATE NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, INDEX IDX_34DCD1767A83DA30 (fk_sex_id), UNIQUE INDEX UNIQ_34DCD1765741EEB9 (fk_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sex (id INT AUTO_INCREMENT NOT NULL, gender VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, need_team TINYINT(1) DEFAULT NULL, image VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_center (id INT AUTO_INCREMENT NOT NULL, fk_services_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, municipality VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, image VARCHAR(512) DEFAULT NULL, phone VARCHAR(255) NOT NULL, INDEX IDX_242F3F85C7030AF5 (fk_services_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_center_sport (sport_center_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_3AA4F82672B00B1A (sport_center_id), INDEX IDX_3AA4F826AC78BCF8 (sport_id), PRIMARY KEY(sport_center_id, sport_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_color (id INT AUTO_INCREMENT NOT NULL, team_a VARCHAR(255) NOT NULL, team_b VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable (id INT AUTO_INCREMENT NOT NULL, fk_sportcenter_id INT DEFAULT NULL, day VARCHAR(255) NOT NULL, open TIME NOT NULL, close TIME NOT NULL, INDEX IDX_6B1F670345EA044 (fk_sportcenter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_players ADD CONSTRAINT FK_A319EA0943DFAB55 FOREIGN KEY (fk_event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE event_players ADD CONSTRAINT FK_A319EA0940226CD7 FOREIGN KEY (fk_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A9E50FF26 FOREIGN KEY (fk_sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A345EA044 FOREIGN KEY (fk_sportcenter_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A98B0E86C FOREIGN KEY (fk_difficulty_id) REFERENCES difficulty (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A7A83DA30 FOREIGN KEY (fk_sex_id) REFERENCES sex (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A40226CD7 FOREIGN KEY (fk_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A7F370AF5 FOREIGN KEY (fk_team_colours_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE events_results ADD CONSTRAINT FK_C9C29D0EFC33C7F5 FOREIGN KEY (fk_events_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE location_sport_center ADD CONSTRAINT FK_55886FEBAB135F2F FOREIGN KEY (fk_sport_center_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1767A83DA30 FOREIGN KEY (fk_sex_id) REFERENCES sex (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1765741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sport_center ADD CONSTRAINT FK_242F3F85C7030AF5 FOREIGN KEY (fk_services_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE sport_center_sport ADD CONSTRAINT FK_3AA4F82672B00B1A FOREIGN KEY (sport_center_id) REFERENCES sport_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sport_center_sport ADD CONSTRAINT FK_3AA4F826AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F670345EA044 FOREIGN KEY (fk_sportcenter_id) REFERENCES sport_center (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_players DROP FOREIGN KEY FK_A319EA0943DFAB55');
        $this->addSql('ALTER TABLE event_players DROP FOREIGN KEY FK_A319EA0940226CD7');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A9E50FF26');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A345EA044');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A98B0E86C');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A7A83DA30');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A40226CD7');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A7F370AF5');
        $this->addSql('ALTER TABLE events_results DROP FOREIGN KEY FK_C9C29D0EFC33C7F5');
        $this->addSql('ALTER TABLE location_sport_center DROP FOREIGN KEY FK_55886FEBAB135F2F');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1767A83DA30');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1765741EEB9');
        $this->addSql('ALTER TABLE sport_center DROP FOREIGN KEY FK_242F3F85C7030AF5');
        $this->addSql('ALTER TABLE sport_center_sport DROP FOREIGN KEY FK_3AA4F82672B00B1A');
        $this->addSql('ALTER TABLE sport_center_sport DROP FOREIGN KEY FK_3AA4F826AC78BCF8');
        $this->addSql('ALTER TABLE timetable DROP FOREIGN KEY FK_6B1F670345EA044');
        $this->addSql('DROP TABLE difficulty');
        $this->addSql('DROP TABLE event_players');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE events_results');
        $this->addSql('DROP TABLE location_sport_center');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE sex');
        $this->addSql('DROP TABLE sport');
        $this->addSql('DROP TABLE sport_center');
        $this->addSql('DROP TABLE sport_center_sport');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE team_color');
        $this->addSql('DROP TABLE timetable');
        $this->addSql('DROP TABLE user');
    }
}

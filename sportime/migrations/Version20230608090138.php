<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608090138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sport_center_services DROP FOREIGN KEY FK_8815AE8D72B00B1A');
        $this->addSql('ALTER TABLE sport_center_services DROP FOREIGN KEY FK_8815AE8DAEF5A6C1');
        $this->addSql('ALTER TABLE sport_center_sport DROP FOREIGN KEY FK_3AA4F82672B00B1A');
        $this->addSql('ALTER TABLE sport_center_sport DROP FOREIGN KEY FK_3AA4F826AC78BCF8');
        $this->addSql('DROP TABLE sport_center_services');
        $this->addSql('DROP TABLE sport_center_sport');
        $this->addSql('ALTER TABLE event_players DROP FOREIGN KEY FK_A319EA0943DFAB55');
        $this->addSql('ALTER TABLE event_players DROP FOREIGN KEY FK_A319EA0940226CD7');
        $this->addSql('DROP INDEX IDX_A319EA0943DFAB55 ON event_players');
        $this->addSql('DROP INDEX IDX_A319EA0940226CD7 ON event_players');
        $this->addSql('ALTER TABLE event_players DROP fk_event_id, DROP fk_person_id, DROP team');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A37F07F1F');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A7A83DA30');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AD0368DE6');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A40226CD7');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A98B0E86C');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A345EA044');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A563F0DEA');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A9E50FF26');
        $this->addSql('DROP INDEX IDX_5387574A345EA044 ON events');
        $this->addSql('DROP INDEX IDX_5387574AD0368DE6 ON events');
        $this->addSql('DROP INDEX IDX_5387574A98B0E86C ON events');
        $this->addSql('DROP INDEX IDX_5387574A563F0DEA ON events');
        $this->addSql('DROP INDEX IDX_5387574A7A83DA30 ON events');
        $this->addSql('DROP INDEX IDX_5387574A9E50FF26 ON events');
        $this->addSql('DROP INDEX IDX_5387574A37F07F1F ON events');
        $this->addSql('DROP INDEX IDX_5387574A40226CD7 ON events');
        $this->addSql('ALTER TABLE events DROP fk_sport_id, DROP fk_sportcenter_id, DROP fk_difficulty_id, DROP fk_sex_id, DROP fk_person_id, DROP fk_teamcolor_id, DROP fk_teamcolor_two_id, DROP fk_state_id');
        $this->addSql('ALTER TABLE events_results DROP FOREIGN KEY FK_C9C29D0E43DFAB55');
        $this->addSql('DROP INDEX IDX_C9C29D0E43DFAB55 ON events_results');
        $this->addSql('ALTER TABLE events_results DROP fk_event_id');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1767A83DA30');
        $this->addSql('DROP INDEX IDX_34DCD1767A83DA30 ON person');
        $this->addSql('ALTER TABLE person DROP fk_sex_id');
        $this->addSql('ALTER TABLE reserved_time DROP FOREIGN KEY FK_3F312D9A57A9834F');
        $this->addSql('ALTER TABLE reserved_time DROP FOREIGN KEY FK_3F312D9AECCDC403');
        $this->addSql('DROP INDEX IDX_3F312D9AECCDC403 ON reserved_time');
        $this->addSql('DROP INDEX IDX_3F312D9A57A9834F ON reserved_time');
        $this->addSql('ALTER TABLE reserved_time DROP fk_sport_center_id_id, DROP fk_event_id_id');
        $this->addSql('ALTER TABLE schedule_center DROP FOREIGN KEY FK_22D1AF3BECCDC403');
        $this->addSql('DROP INDEX IDX_22D1AF3BECCDC403 ON schedule_center');
        $this->addSql('ALTER TABLE schedule_center DROP fk_sport_center_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sport_center_services (sport_center_id INT NOT NULL, services_id INT NOT NULL, INDEX IDX_8815AE8D72B00B1A (sport_center_id), INDEX IDX_8815AE8DAEF5A6C1 (services_id), PRIMARY KEY(sport_center_id, services_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sport_center_sport (sport_center_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_3AA4F82672B00B1A (sport_center_id), INDEX IDX_3AA4F826AC78BCF8 (sport_id), PRIMARY KEY(sport_center_id, sport_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sport_center_services ADD CONSTRAINT FK_8815AE8D72B00B1A FOREIGN KEY (sport_center_id) REFERENCES sport_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sport_center_services ADD CONSTRAINT FK_8815AE8DAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sport_center_sport ADD CONSTRAINT FK_3AA4F82672B00B1A FOREIGN KEY (sport_center_id) REFERENCES sport_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sport_center_sport ADD CONSTRAINT FK_3AA4F826AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events ADD fk_sport_id INT DEFAULT NULL, ADD fk_sportcenter_id INT DEFAULT NULL, ADD fk_difficulty_id INT DEFAULT NULL, ADD fk_sex_id INT DEFAULT NULL, ADD fk_person_id INT DEFAULT NULL, ADD fk_teamcolor_id INT DEFAULT NULL, ADD fk_teamcolor_two_id INT DEFAULT NULL, ADD fk_state_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A37F07F1F FOREIGN KEY (fk_state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A7A83DA30 FOREIGN KEY (fk_sex_id) REFERENCES sex (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AD0368DE6 FOREIGN KEY (fk_teamcolor_id) REFERENCES team_color (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A40226CD7 FOREIGN KEY (fk_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A98B0E86C FOREIGN KEY (fk_difficulty_id) REFERENCES difficulty (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A345EA044 FOREIGN KEY (fk_sportcenter_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A563F0DEA FOREIGN KEY (fk_teamcolor_two_id) REFERENCES team_color (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A9E50FF26 FOREIGN KEY (fk_sport_id) REFERENCES sport (id)');
        $this->addSql('CREATE INDEX IDX_5387574A345EA044 ON events (fk_sportcenter_id)');
        $this->addSql('CREATE INDEX IDX_5387574AD0368DE6 ON events (fk_teamcolor_id)');
        $this->addSql('CREATE INDEX IDX_5387574A98B0E86C ON events (fk_difficulty_id)');
        $this->addSql('CREATE INDEX IDX_5387574A563F0DEA ON events (fk_teamcolor_two_id)');
        $this->addSql('CREATE INDEX IDX_5387574A7A83DA30 ON events (fk_sex_id)');
        $this->addSql('CREATE INDEX IDX_5387574A9E50FF26 ON events (fk_sport_id)');
        $this->addSql('CREATE INDEX IDX_5387574A37F07F1F ON events (fk_state_id)');
        $this->addSql('CREATE INDEX IDX_5387574A40226CD7 ON events (fk_person_id)');
        $this->addSql('ALTER TABLE events_results ADD fk_event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events_results ADD CONSTRAINT FK_C9C29D0E43DFAB55 FOREIGN KEY (fk_event_id) REFERENCES events (id)');
        $this->addSql('CREATE INDEX IDX_C9C29D0E43DFAB55 ON events_results (fk_event_id)');
        $this->addSql('ALTER TABLE event_players ADD fk_event_id INT DEFAULT NULL, ADD fk_person_id INT DEFAULT NULL, ADD team INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event_players ADD CONSTRAINT FK_A319EA0943DFAB55 FOREIGN KEY (fk_event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE event_players ADD CONSTRAINT FK_A319EA0940226CD7 FOREIGN KEY (fk_person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_A319EA0943DFAB55 ON event_players (fk_event_id)');
        $this->addSql('CREATE INDEX IDX_A319EA0940226CD7 ON event_players (fk_person_id)');
        $this->addSql('ALTER TABLE person ADD fk_sex_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1767A83DA30 FOREIGN KEY (fk_sex_id) REFERENCES sex (id)');
        $this->addSql('CREATE INDEX IDX_34DCD1767A83DA30 ON person (fk_sex_id)');
        $this->addSql('ALTER TABLE reserved_time ADD fk_sport_center_id_id INT DEFAULT NULL, ADD fk_event_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reserved_time ADD CONSTRAINT FK_3F312D9A57A9834F FOREIGN KEY (fk_event_id_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE reserved_time ADD CONSTRAINT FK_3F312D9AECCDC403 FOREIGN KEY (fk_sport_center_id_id) REFERENCES sport_center (id)');
        $this->addSql('CREATE INDEX IDX_3F312D9AECCDC403 ON reserved_time (fk_sport_center_id_id)');
        $this->addSql('CREATE INDEX IDX_3F312D9A57A9834F ON reserved_time (fk_event_id_id)');
        $this->addSql('ALTER TABLE schedule_center ADD fk_sport_center_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule_center ADD CONSTRAINT FK_22D1AF3BECCDC403 FOREIGN KEY (fk_sport_center_id_id) REFERENCES sport_center (id)');
        $this->addSql('CREATE INDEX IDX_22D1AF3BECCDC403 ON schedule_center (fk_sport_center_id_id)');
    }
}

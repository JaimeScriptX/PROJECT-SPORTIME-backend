<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230528114109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reserved_time (id INT AUTO_INCREMENT NOT NULL, fk_sport_center_id_id INT DEFAULT NULL, fk_event_id_id INT DEFAULT NULL, day INT NOT NULL, date DATE NOT NULL, start TIME NOT NULL, end TIME NOT NULL, date_created DATE NOT NULL, canceled TINYINT(1) NOT NULL, cancellation_reason VARCHAR(255) DEFAULT NULL, INDEX IDX_3F312D9AECCDC403 (fk_sport_center_id_id), INDEX IDX_3F312D9A57A9834F (fk_event_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reserved_time ADD CONSTRAINT FK_3F312D9AECCDC403 FOREIGN KEY (fk_sport_center_id_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE reserved_time ADD CONSTRAINT FK_3F312D9A57A9834F FOREIGN KEY (fk_event_id_id) REFERENCES events (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reserved_time DROP FOREIGN KEY FK_3F312D9AECCDC403');
        $this->addSql('ALTER TABLE reserved_time DROP FOREIGN KEY FK_3F312D9A57A9834F');
        $this->addSql('DROP TABLE reserved_time');
    }
}

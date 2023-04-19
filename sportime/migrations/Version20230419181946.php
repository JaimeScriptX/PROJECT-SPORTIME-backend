<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419181946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_players (id INT AUTO_INCREMENT NOT NULL, fk_person_id INT NOT NULL, fk_event_id INT NOT NULL, INDEX IDX_A319EA0940226CD7 (fk_person_id), INDEX IDX_A319EA0943DFAB55 (fk_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_players ADD CONSTRAINT FK_A319EA0940226CD7 FOREIGN KEY (fk_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE event_players ADD CONSTRAINT FK_A319EA0943DFAB55 FOREIGN KEY (fk_event_id) REFERENCES events (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_players DROP FOREIGN KEY FK_A319EA0940226CD7');
        $this->addSql('ALTER TABLE event_players DROP FOREIGN KEY FK_A319EA0943DFAB55');
        $this->addSql('DROP TABLE event_players');
    }
}

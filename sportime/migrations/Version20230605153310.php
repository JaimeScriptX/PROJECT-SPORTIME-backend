<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230605153310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D9345EA044');
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D99E50FF26');
        $this->addSql('DROP TABLE price');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE price (id INT AUTO_INCREMENT NOT NULL, fk_sport_id INT DEFAULT NULL, fk_sportcenter_id INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_CAC822D9345EA044 (fk_sportcenter_id), INDEX IDX_CAC822D99E50FF26 (fk_sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D9345EA044 FOREIGN KEY (fk_sportcenter_id) REFERENCES sport_center (id)');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D99E50FF26 FOREIGN KEY (fk_sport_id) REFERENCES sport (id)');
    }
}

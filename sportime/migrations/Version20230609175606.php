<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609175606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorites (id INT AUTO_INCREMENT NOT NULL, fk_person_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', fk_sport_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_E46960F540226CD7 (fk_person_id), INDEX IDX_E46960F59E50FF26 (fk_sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F540226CD7 FOREIGN KEY (fk_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F59E50FF26 FOREIGN KEY (fk_sport_id) REFERENCES sport (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F540226CD7');
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F59E50FF26');
        $this->addSql('DROP TABLE favorites');
    }
}

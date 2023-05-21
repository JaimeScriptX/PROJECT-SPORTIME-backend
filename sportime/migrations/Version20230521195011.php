<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521195011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_center_services (sport_center_id INT NOT NULL, services_id INT NOT NULL, INDEX IDX_8815AE8D72B00B1A (sport_center_id), INDEX IDX_8815AE8DAEF5A6C1 (services_id), PRIMARY KEY(sport_center_id, services_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sport_center_services ADD CONSTRAINT FK_8815AE8D72B00B1A FOREIGN KEY (sport_center_id) REFERENCES sport_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sport_center_services ADD CONSTRAINT FK_8815AE8DAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sport_center_services DROP FOREIGN KEY FK_8815AE8D72B00B1A');
        $this->addSql('ALTER TABLE sport_center_services DROP FOREIGN KEY FK_8815AE8DAEF5A6C1');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE sport_center_services');
    }
}

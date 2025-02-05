<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208084002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_right (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_5D5F0947A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_right ADD CONSTRAINT FK_5D5F0947A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE external_api_token CHANGE token token LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_right DROP FOREIGN KEY FK_5D5F0947A76ED395');
        $this->addSql('DROP TABLE access_right');
        $this->addSql('ALTER TABLE external_api_token CHANGE token token VARCHAR(255) NOT NULL');
    }
}

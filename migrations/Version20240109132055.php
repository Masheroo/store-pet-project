<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109132055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lot_discount (id INT AUTO_INCREMENT NOT NULL, lot_id INT NOT NULL, count_of_purchases INT NOT NULL, discount DOUBLE PRECISION NOT NULL, INDEX IDX_432A453AA8CBA5F7 (lot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lot_discount ADD CONSTRAINT FK_432A453AA8CBA5F7 FOREIGN KEY (lot_id) REFERENCES lot (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lot_discount DROP FOREIGN KEY FK_432A453AA8CBA5F7');
        $this->addSql('DROP TABLE lot_discount');
    }
}

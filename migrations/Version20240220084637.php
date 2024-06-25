<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220084637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lot_field_value (lot_id INT NOT NULL, field_value_id INT NOT NULL, INDEX IDX_63A47873A8CBA5F7 (lot_id), INDEX IDX_63A478732F183C6F (field_value_id), PRIMARY KEY(lot_id, field_value_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lot_field_value ADD CONSTRAINT FK_63A47873A8CBA5F7 FOREIGN KEY (lot_id) REFERENCES lot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lot_field_value ADD CONSTRAINT FK_63A478732F183C6F FOREIGN KEY (field_value_id) REFERENCES field_value (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lot_field_value DROP FOREIGN KEY FK_63A47873A8CBA5F7');
        $this->addSql('ALTER TABLE lot_field_value DROP FOREIGN KEY FK_63A478732F183C6F');
        $this->addSql('DROP TABLE lot_field_value');
    }
}

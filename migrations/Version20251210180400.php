<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20251210180400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unit column from shopping_list_item table';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE shopping_list_item DROP COLUMN unit');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE shopping_list_item ADD unit VARCHAR(50) DEFAULT NULL');
    }
}

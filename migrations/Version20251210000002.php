<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table User et des relations avec Recipe, Ingredient et ShoppingList';
    }

    public function up(Schema $schema): void
    {
        // 1. Créer la table user
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 2. Créer un utilisateur par défaut pour les données existantes
        // Email: admin@example.com
        // Mot de passe: password
        $this->addSql("INSERT INTO user (email, roles, password) VALUES ('admin@example.com', '[]', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')");

        // 3. Ajouter les colonnes user_id comme NULL d'abord
        $this->addSql('ALTER TABLE ingredient ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shopping_list ADD user_id INT DEFAULT NULL');

        // 4. Assigner toutes les données existantes à l'utilisateur par défaut (id=1)
        $this->addSql('UPDATE ingredient SET user_id = 1 WHERE user_id IS NULL');
        $this->addSql('UPDATE recipe SET user_id = 1 WHERE user_id IS NULL');
        $this->addSql('UPDATE shopping_list SET user_id = 1 WHERE user_id IS NULL');

        // 5. Maintenant rendre les colonnes NOT NULL
        $this->addSql('ALTER TABLE ingredient MODIFY user_id INT NOT NULL');
        $this->addSql('ALTER TABLE recipe MODIFY user_id INT NOT NULL');
        $this->addSql('ALTER TABLE shopping_list MODIFY user_id INT NOT NULL');

        // 6. Ajouter les contraintes de clé étrangère et les index
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_INGREDIENT_USER FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_INGREDIENT_USER ON ingredient (user_id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_RECIPE_USER FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_RECIPE_USER ON recipe (user_id)');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_SHOPPING_LIST_USER FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_SHOPPING_LIST_USER ON shopping_list (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_INGREDIENT_USER');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_RECIPE_USER');
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_SHOPPING_LIST_USER');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_INGREDIENT_USER ON ingredient');
        $this->addSql('ALTER TABLE ingredient DROP user_id');
        $this->addSql('DROP INDEX IDX_RECIPE_USER ON recipe');
        $this->addSql('ALTER TABLE recipe DROP user_id');
        $this->addSql('DROP INDEX IDX_SHOPPING_LIST_USER ON shopping_list');
        $this->addSql('ALTER TABLE shopping_list DROP user_id');
    }
}


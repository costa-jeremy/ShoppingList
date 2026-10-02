# Fonctionnalité : Ajustement des ingrédients dans les listes de courses

## Vue d'ensemble

Cette fonctionnalité permet aux utilisateurs de personnaliser leur liste de courses en :
- **Masquant des ingrédients** (en décochant la case à côté de l'ingrédient)
- **Modifiant les quantités** (en changeant la valeur dans le champ quantité)

## Fichiers créés

### 1. Entité `ShoppingListIngredientAdjustment`
- **Chemin** : `src/Entity/ShoppingListIngredientAdjustment.php`
- **Rôle** : Stocke les ajustements (quantité modifiée ou masquage) pour chaque ingrédient de chaque liste
- **Champs** :
  - `shoppingList` : Relation vers la liste de courses
  - `ingredient` : Relation vers l'ingrédient
  - `adjustedQuantity` : Nouvelle quantité (nullable)
  - `isHidden` : Boolean pour masquer l'ingrédient

### 2. Repository `ShoppingListIngredientAdjustmentRepository`
- **Chemin** : `src/Repository/ShoppingListIngredientAdjustmentRepository.php`
- **Rôle** : Gestion des requêtes pour les ajustements

### 3. Migration
- **Chemin** : `migrations/Version20251228000000.php`
- **Rôle** : Crée la table `shopping_list_ingredient_adjustment`

## Modifications apportées

### 1. Entité `ShoppingList.php`
- Ajout de la relation `OneToMany` vers `ShoppingListIngredientAdjustment`
- Modification de la méthode `getTotalIngredients()` pour prendre en compte les ajustements

### 2. Contrôleur `ShoppingListController.php`
- Ajout de la route `adjustIngredient()` pour gérer les modifications via AJAX
- Endpoint : `/shopping/list/{id}/adjust-ingredient` (POST)

### 3. Template `show.html.twig`
- Ajout de checkboxes pour masquer/afficher les ingrédients
- Ajout de champs input pour modifier les quantités
- Ajout du JavaScript pour gérer les interactions et les appels AJAX

### 4. Template `pdf.html.twig`
- Modification pour afficher les quantités ajustées
- Masquage automatique des ingrédients cachés dans le PDF

## Utilisation

### Dans la page "Détails de la liste de courses" :

1. **Pour masquer un ingrédient** :
   - Décocher la case à côté de l'ingrédient
   - L'ingrédient devient grisé et barré
   - Il n'apparaîtra plus dans le PDF

2. **Pour modifier une quantité** :
   - Cliquer dans le champ "Quantité totale"
   - Saisir la nouvelle valeur
   - La modification est sauvegardée automatiquement après 500ms

3. **Pour réafficher un ingrédient masqué** :
   - Cocher à nouveau la case
   - L'ingrédient redevient actif

### Dans le PDF :
- Seuls les ingrédients non masqués sont affichés
- Les quantités ajustées remplacent les quantités calculées

## Points techniques

- Les modifications sont sauvegardées en temps réel via AJAX
- Utilisation de `debounce` (500ms) pour éviter trop d'appels lors de la saisie
- Si un ingrédient est démasqué ET qu'il n'a pas de quantité ajustée, l'ajustement est supprimé de la base de données
- Les ajustements sont liés à la liste de courses (suppression en cascade)

## Migration à exécuter

```bash
php bin/console doctrine:migrations:migrate
```

Cette commande créera la table `shopping_list_ingredient_adjustment` avec les contraintes appropriées.


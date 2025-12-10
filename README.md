# 🍽️ Application de Listes de Courses

Application Symfony pour gérer vos recettes, ingrédients et listes de courses.

## 📚 Documentation

- **[Guide PDF](PDF_GUIDE.md)** - Configuration et personnalisation des PDFs de listes de courses
- **[Compteur de recettes](COMPTEUR_RECETTES.md)** - Comment utiliser le système de suivi des recettes
- **[Guide de suppression](SUPPRESSION_GUIDE.md)** - Supprimer des recettes, ingrédients et listes en toute sécurité
- **[Guide Responsive](RESPONSIVE_GUIDE.md)** - Améliorations mobile et responsive design

## 🎯 Fonctionnalités principales

### 🍳 Gestion des Recettes
- ✅ Créer, modifier, supprimer des recettes
- ✅ Ajouter des commentaires (notes, conseils de préparation)
- ✅ Associer des ingrédients avec quantités
- ✅ **Compteur de réalisations** : suivez combien de fois vous avez fait chaque recette
- ✅ Statistiques : dernière préparation, nombre de fois, badges de progression
- ✅ **Suppression sécurisée** : avec confirmation et protection CSRF

### 🥕 Gestion des Ingrédients
- ✅ Créer, modifier des ingrédients
- ✅ Définir des unités (kg, L, pièce, etc.)
- ✅ Association automatique avec les recettes
- ✅ **Suppression avec alerte** : prévient si l'ingrédient est utilisé dans des recettes

### 📋 Listes de Courses
- ✅ Créer des listes avec un nom personnalisé
- ✅ Sélectionner plusieurs recettes
- ✅ **Cumul automatique** des quantités d'ingrédients
- ✅ Génération de PDF professionnel
- ✅ Affichage détaillé par recette et total récapitulatif
- ✅ **Suppression sans impact** sur les recettes

### 📄 Export PDF
- ✅ Design moderne et professionnel
- ✅ Recettes avec commentaires et ingrédients
- ✅ Tableau récapitulatif des courses (quantités cumulées)
- ✅ Nom de fichier personnalisé selon la liste

## 🚀 Utilisation rapide

### Marquer une recette comme faite
1. Ouvrez une recette
2. Cliquez sur **"J'ai fait cette recette !"**
3. Le compteur s'incrémente automatiquement

### Créer une liste de courses
1. Allez dans "Mes listes de courses"
2. Cliquez sur "Nouvelle liste"
3. Donnez un nom à votre liste
4. Sélectionnez les recettes désirées
5. Enregistrez
6. Les ingrédients sont automatiquement cumulés !

### Télécharger un PDF
1. Ouvrez une liste de courses
2. Cliquez sur "Télécharger PDF"
3. Le PDF s'ouvre avec toutes les informations

### Supprimer un élément
1. **Depuis la liste** : Cliquez sur le bouton rouge "Supprimer"
2. **Lisez l'alerte** : Attention particulière pour les ingrédients !
3. **Confirmez** : L'action est irréversible
4. ✅ Message de confirmation affiché

> ⚠️ **Attention** : Supprimer un ingrédient le retire de TOUTES les recettes !

## 🛠️ Installation

### Prérequis
- PHP 8.0+
- Composer
- Symfony CLI (optionnel)
- MySQL/MariaDB

### Installation des dépendances
```bash
composer install
npm install
```

### Base de données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### Permissions wkhtmltopdf
Si vous avez une erreur de permissions pour le PDF :
```bash
./fix-wkhtmltopdf-permissions.sh
# ou
chmod +x vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64
```

### Compiler les assets
```bash
npm run build
# ou en mode watch
npm run watch
```

### Lancer le serveur
```bash
symfony serve
# ou
php -S localhost:8000 -t public
```

## 📊 Structure du projet

```
src/
├── Controller/
│   ├── RecipeController.php          # Gestion des recettes + compteur
│   ├── IngredientController.php      # Gestion des ingrédients + API
│   └── ShoppingListController.php    # Listes de courses + PDF
├── Entity/
│   ├── Recipe.php                     # Entité recette avec compteur
│   ├── Ingredient.php                 # Entité ingrédient
│   ├── RecipeIngredient.php          # Liaison recette-ingrédient
│   └── ShoppingList.php              # Entité liste avec cumul auto
└── Form/
    ├── RecipeType.php
    ├── IngredientType.php
    └── ShoppingListType.php

templates/
├── recipe/
│   ├── index.html.twig               # Liste avec badges colorés
│   ├── show.html.twig                # Détails + bouton "J'ai fait"
│   └── _form.html.twig               # Formulaire avec ingrédients
├── shopping_list/
│   ├── show.html.twig                # Affichage liste + cumul
│   └── pdf.html.twig                 # Template PDF
└── base.html.twig                    # Template de base + flash messages
```

## 🎨 Personnalisation

### Modifier les seuils des badges
Éditez `templates/recipe/index.html.twig` pour changer les couleurs selon le nombre de fois.

### Personnaliser le PDF
Éditez `templates/shopping_list/pdf.html.twig` pour modifier le design.

### Changer les messages motivants
Éditez `templates/recipe/show.html.twig` pour personnaliser les messages de progression.

## 🐛 Dépannage

### Problème de permissions PDF
```bash
chmod +x vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64
```

### L'unité ne s'affiche pas
Vérifiez que l'API `/ingredient/{id}/api` fonctionne et que JavaScript est activé.

### Les ingrédients ne se cumulent pas
La méthode `getTotalIngredients()` dans `ShoppingList` additionne automatiquement les quantités des ingrédients identiques.

## 📝 Licence

Propriétaire

## 👨‍💻 Auteur

Développé avec ❤️ et Symfony


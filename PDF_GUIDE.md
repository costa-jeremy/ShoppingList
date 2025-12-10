# 📋 Liste de Courses - Guide PDF

## 🎯 Fonctionnalités du PDF

Le PDF généré pour une liste de courses contient :

### 1. En-tête
- Nom de la liste de courses
- Date de création
- Nombre de recettes sélectionnées

### 2. Section "Recettes sélectionnées"
Pour chaque recette :
- **Titre de la recette** (en bleu foncé)
- **Commentaire** (si présent, affiché en gris italique avec icône 💡)
- **Tableau des ingrédients** avec :
  - Nom de l'ingrédient
  - Quantité nécessaire
  - Unité

### 3. Section "Liste de courses totale"
- Tableau récapitulatif avec **quantités cumulées** pour tous les ingrédients
- Si une même ingrédient est dans plusieurs recettes, les quantités sont additionnées

### 4. Pied de page
- Date et heure de génération du PDF

## 🔧 Configuration de wkhtmltopdf

### Problème de permissions

Si vous rencontrez l'erreur :
```
Permission denied: /var/www/html/projectshoppinglist/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64
```

### Solutions :

#### Solution 1 : Script automatique
```bash
./fix-wkhtmltopdf-permissions.sh
```

#### Solution 2 : Commande manuelle
```bash
chmod +x vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64
```

#### Solution 3 : Automatique après composer
Les scripts post-install et post-update dans `composer.json` corrigent automatiquement les permissions après chaque `composer install` ou `composer update`.

## 📝 Nom du fichier PDF

Le fichier PDF généré aura le format :
```
{nom_de_la_liste}_{date}.pdf
```

Exemple : `courses_weekend_2025-12-10.pdf`

Si la liste n'a pas de nom, le fichier sera nommé : `liste_courses_2025-12-10.pdf`

## 🎨 Personnalisation

Le template PDF se trouve dans :
```
templates/shopping_list/pdf.html.twig
```

Vous pouvez personnaliser :
- Les couleurs (dans la section `<style>`)
- La mise en page
- Les informations affichées
- Le format des tableaux


# 📊 Tableau Récapitulatif des Actions

## Actions Disponibles par Type d'Élément

| Action | 🍳 Recettes | 🥕 Ingrédients | 📋 Listes de Courses |
|--------|------------|---------------|---------------------|
| **Créer** | ✅ Nouvelle recette | ✅ Nouvel ingrédient | ✅ Nouvelle liste |
| **Voir** | ✅ Page de détails | ❌ Pas de page show | ✅ Page de détails |
| **Modifier** | ✅ Édition complète | ✅ Édition complète | ✅ Édition complète |
| **Supprimer** | ✅ Index + Show | ✅ Index uniquement | ✅ Index + Show |
| **Marquer comme fait** | ✅ Compteur | ❌ N/A | ❌ N/A |
| **Télécharger PDF** | ❌ N/A | ❌ N/A | ✅ Oui |

## Emplacements des Boutons de Suppression

### 🍳 Recettes
- ✅ **Liste des recettes** (`/recipe/`)
  - Bouton rouge "Supprimer" dans la colonne Actions
- ✅ **Détails d'une recette** (`/recipe/{id}`)
  - Bouton "Supprimer" dans le footer de la carte

### 🥕 Ingrédients
- ✅ **Liste des ingrédients** (`/ingredient/`)
  - Bouton rouge "Supprimer" dans la colonne Actions
- ❌ **Pas de page de détails** pour les ingrédients

### 📋 Listes de Courses
- ✅ **Liste des listes** (`/shopping/list/`)
  - Bouton rouge "Supprimer" dans la colonne Actions
- ✅ **Détails d'une liste** (`/shopping/list/{id}/show`)
  - Bouton "Supprimer" dans le footer de la carte

## Messages de Confirmation

| Type | Message |
|------|---------|
| **Recette** | "Êtes-vous sûr de vouloir supprimer cette recette ?" |
| **Ingrédient** | "Êtes-vous sûr de vouloir supprimer cet ingrédient ? Attention, il sera supprimé de toutes les recettes qui l'utilisent !" |
| **Liste** | "Êtes-vous sûr de vouloir supprimer cette liste de courses ?" |

## Impact des Suppressions

| Suppression de... | Impact sur... | Gravité |
|-------------------|---------------|---------|
| **Recette** | RecipeIngredient (supprimés) | 🟡 Moyen |
| | Ingredient (conservés) | ✅ Aucun |
| | ShoppingList (recette retirée) | ✅ Faible |
| **Ingrédient** | RecipeIngredient (supprimés) | 🔴 ÉLEVÉ |
| | Recipe (perd cet ingrédient) | 🔴 ÉLEVÉ |
| **Liste** | Relations (supprimées) | ✅ Aucun |
| | Recipe (conservées) | ✅ Aucun |

## Codes Couleur des Boutons

| Couleur | Action | Classe CSS |
|---------|--------|-----------|
| 🔵 Bleu | Voir | `btn-info` |
| 🟡 Jaune | Modifier | `btn-warning` |
| 🟢 Vert | Actions positives | `btn-success` |
| 🔴 Rouge | Supprimer | `btn-danger` |
| ⚫ Gris | Secondaire | `btn-secondary` |

## Sécurité

| Protection | Recette | Ingrédient | Liste |
|------------|---------|------------|-------|
| **Token CSRF** | ✅ | ✅ | ✅ |
| **Confirmation JS** | ✅ | ✅ | ✅ |
| **Méthode POST** | ✅ | ✅ | ✅ |
| **Message Flash** | ✅ | ✅ | ✅ |
| **Alerte spéciale** | ❌ | ✅ Impact élevé | ❌ |

## Flux de Suppression

### Depuis l'Index
```
Liste → Clic "Supprimer" → Popup Confirmation → POST request → 
Flash Message → Redirection Index
```

### Depuis la Page Show
```
Détails → Clic "Supprimer" (footer) → Popup Confirmation → POST request → 
Flash Message → Redirection Index
```

## Routes API

| Route | Méthode | Nom | Redirection |
|-------|---------|-----|-------------|
| `/recipe/{id}` | POST | `app_recipe_delete` | `app_recipe_index` |
| `/ingredient/{id}/delete` | POST | `app_ingredient_delete` | `app_ingredient_index` |
| `/shopping/list/{id}/delete` | POST | `app_shopping_list_delete` | `app_shopping_list_index` |

## Messages Flash de Succès

| Type | Message |
|------|---------|
| **Recette** | "La recette a été supprimée avec succès." |
| **Ingrédient** | "L'ingrédient a été supprimé avec succès." |
| **Liste** | "La liste de courses a été supprimée avec succès." |

## Statistiques d'Utilisation Recommandées

### 🟢 Suppression Fréquente (Sans Risque)
- Listes de courses anciennes
- Listes de courses de test
- Recettes en double

### 🟡 Suppression Occasionnelle (Risque Moyen)
- Recettes pas très utilisées
- Recettes avec peu d'ingrédients

### 🔴 Suppression Rare (Risque Élevé)
- Ingrédients de base (sel, poivre, huile...)
- Ingrédients utilisés dans beaucoup de recettes
- Recettes favorites (haut compteur)

## Checklist Avant Suppression

### ✅ Ingrédient
- [ ] Vérifier combien de recettes l'utilisent
- [ ] Envisager de le renommer plutôt que le supprimer
- [ ] Préparer la liste des recettes à mettre à jour
- [ ] Lire attentivement le message d'alerte

### ✅ Recette
- [ ] Vérifier le compteur (si haut, c'est une favorite !)
- [ ] Noter les ingrédients si besoin de la recréer
- [ ] Vérifier si elle est dans des listes de courses actives

### ✅ Liste de Courses
- [ ] Télécharger le PDF si besoin
- [ ] Vérifier la date de création
- [ ] S'assurer que ce n'est pas la mauvaise liste

## Raccourcis Clavier (Futur)

Suggestions pour amélioration future :

| Raccourci | Action |
|-----------|--------|
| `Suppr` | Supprimer l'élément sélectionné |
| `Ctrl+Z` | Annuler la dernière suppression |
| `Échap` | Annuler la confirmation |


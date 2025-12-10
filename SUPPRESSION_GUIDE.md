# 🗑️ Guide de Suppression - Recettes, Ingrédients et Listes de Courses

## 🎯 Vue d'ensemble

L'application permet de supprimer tous les éléments (recettes, ingrédients, listes de courses) avec confirmation pour éviter les suppressions accidentelles.

## 🍳 Supprimer une Recette

### Depuis la liste des recettes
1. Allez dans **"Mes recettes"**
2. Trouvez la recette à supprimer
3. Cliquez sur le bouton rouge **"Supprimer"** 🗑️
4. Confirmez dans la popup
5. ✅ La recette est supprimée !

### Depuis la page de détails
1. Ouvrez une recette
2. En bas de la page, cliquez sur **"Supprimer"**
3. Confirmez
4. ✅ Supprimée et redirection vers la liste

### ⚠️ Important
- La suppression d'une recette **supprime aussi** :
  - Tous ses ingrédients associés (les liaisons RecipeIngredient)
  - Les références dans les listes de courses
- **L'action est irréversible !**

## 🥕 Supprimer un Ingrédient

### Depuis la liste des ingrédients
1. Allez dans **"Mes ingrédients"**
2. Trouvez l'ingrédient à supprimer
3. Cliquez sur **"Supprimer"** 🗑️
4. **Message d'alerte spécial** : "Attention, il sera supprimé de toutes les recettes qui l'utilisent !"
5. Confirmez
6. ✅ L'ingrédient est supprimé !

### ⚠️ ATTENTION - Impact important !
La suppression d'un ingrédient :
- ❌ **Supprime l'ingrédient de TOUTES les recettes** qui l'utilisent
- ❌ Peut laisser des recettes sans ingrédients
- ❌ **Action irréversible !**

### 💡 Conseil
Avant de supprimer un ingrédient :
1. Vérifiez s'il est utilisé dans des recettes
2. Envisagez plutôt de **modifier** l'ingrédient si c'est juste une correction
3. Si vous devez le supprimer, préparez-vous à mettre à jour les recettes concernées

## 📋 Supprimer une Liste de Courses

### Depuis la liste des listes de courses
1. Allez dans **"Mes listes de courses"**
2. Trouvez la liste à supprimer
3. Cliquez sur le bouton rouge **"Supprimer"** 🗑️
4. Confirmez dans la popup
5. ✅ La liste est supprimée !

### Depuis la page de détails
1. Ouvrez une liste de courses
2. En bas de la page, cliquez sur **"Supprimer"**
3. Confirmez
4. ✅ Supprimée et redirection vers la liste

### ✅ Sécurité
La suppression d'une liste de courses :
- ✅ **Ne supprime PAS** les recettes
- ✅ Supprime uniquement la liste et ses sélections
- ✅ Impact limité - sans danger pour vos données

## 🔒 Sécurité CSRF

Toutes les suppressions sont protégées par :
- **Token CSRF** : Protection contre les attaques Cross-Site Request Forgery
- **Confirmation JavaScript** : Popup de confirmation avant suppression
- **Méthode POST** : Impossible de supprimer via un simple lien

## 🎨 Interface utilisateur

### Codes couleur
- 🔵 **Bleu (Info)** : Voir les détails
- 🟡 **Jaune (Warning)** : Modifier
- 🟢 **Vert (Success)** : Actions positives (PDF, marquer comme fait)
- 🔴 **Rouge (Danger)** : Supprimer

### Messages de confirmation

#### Recette
```
Êtes-vous sûr de vouloir supprimer cette recette ?
```

#### Ingrédient
```
Êtes-vous sûr de vouloir supprimer cet ingrédient ? 
Attention, il sera supprimé de toutes les recettes qui l'utilisent !
```

#### Liste de courses
```
Êtes-vous sûr de vouloir supprimer cette liste de courses ?
```

## 📊 Messages de succès

Après chaque suppression, un message flash vert s'affiche :

- ✅ **Recette** : "La recette a été supprimée avec succès."
- ✅ **Ingrédient** : "L'ingrédient a été supprimé avec succès."
- ✅ **Liste** : "La liste de courses a été supprimée avec succès."

## 🛠️ Implémentation technique

### Routes de suppression
- `/recipe/{id}` (POST) → `app_recipe_delete`
- `/ingredient/{id}/delete` (POST) → `app_ingredient_delete`
- `/shopping/list/{id}/delete` (POST) → `app_shopping_list_delete`

### Protection Doctrine
Les relations sont configurées avec `orphanRemoval` et `cascade` pour gérer automatiquement les suppressions en cascade :

```php
// Dans Recipe.php
#[ORM\OneToMany(
    mappedBy: 'recipe', 
    targetEntity: RecipeIngredient::class, 
    orphanRemoval: true,  // Supprime les RecipeIngredient orphelins
    cascade: ['persist']
)]
```

## ⚡ Cascade de suppression

### Supprimer une Recette
```
Recipe
  └─ RecipeIngredient (supprimé automatiquement)
      └─ Ingredient (conservé)
```

### Supprimer un Ingrédient
```
Ingredient
  └─ RecipeIngredient (supprimé automatiquement)
      └─ Recipe (conservée, mais perd cet ingrédient)
```

### Supprimer une Liste de Courses
```
ShoppingList
  └─ Relations avec Recipe (supprimées)
      └─ Recipe (conservée)
```

## 🔄 Alternatives à la suppression

### Pour les Recettes
- **Marquer comme archivée** (fonctionnalité future)
- Modifier le nom en ajoutant "[ARCHIVE]"
- Exporter en PDF avant de supprimer

### Pour les Ingrédients
- **Modifier** au lieu de supprimer
- Renommer si c'était une erreur de frappe
- Changer l'unité si c'était le problème

### Pour les Listes
- **Créer une nouvelle version** plutôt que supprimer
- Modifier les recettes sélectionnées
- Télécharger le PDF avant de supprimer

## 💡 Bonnes pratiques

### ✅ À faire
- Télécharger un PDF avant de supprimer une liste importante
- Vérifier les recettes affectées avant de supprimer un ingrédient
- Utiliser la confirmation avec attention

### ❌ À éviter
- Supprimer par réflexe sans lire la confirmation
- Supprimer des ingrédients utilisés partout (tomate, sel, etc.)
- Supprimer une liste sans l'avoir consultée

## 🆘 En cas d'erreur

Si vous supprimez quelque chose par erreur :

### Recette
- ❌ **Pas de restauration automatique**
- ✅ Recréez la recette manuellement
- ✅ Consultez une liste de courses PDF si vous l'aviez générée

### Ingrédient
- ❌ **Pas de restauration automatique**
- ✅ Recréez l'ingrédient
- ✅ Réajoutez-le manuellement aux recettes concernées

### Liste de courses
- ❌ **Pas de restauration automatique**
- ✅ Recréez la liste
- ✅ Consultez le PDF si vous l'aviez téléchargé

## 🔮 Améliorations futures possibles

- [ ] **Corbeille** : Garder les éléments supprimés pendant 30 jours
- [ ] **Confirmation renforcée** : Taper "SUPPRIMER" pour confirmer
- [ ] **Historique** : Log des suppressions
- [ ] **Restauration** : Annuler une suppression récente
- [ ] **Protection** : Bloquer la suppression d'ingrédients très utilisés
- [ ] **Export automatique** : Sauvegarder avant suppression


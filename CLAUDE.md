# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Projet

Application Symfony 6.0 (PHP ≥ 8.0.2, Doctrine ORM 2, Twig, Webpack Encore + Stimulus + Bootstrap 5) pour gérer des recettes, des ingrédients et des listes de courses avec export PDF. L'interface, les messages flash, les commentaires et les guides `*.md` sont en français : garder cette langue.

Le projet tourne dans la stack Docker partagée `/home/jeremy/Docker/docker-compose.yml`. Le conteneur à utiliser est **`php-8.0.23`** : `laminas/laminas-code` (verrouillé) impose PHP < 8.2, donc `php-8.4` / `php-8-latest` ne conviennent pas, `php-8.1` marcherait aussi. La base est le conteneur MariaDB (hôte `db-mariadb`, `DATABASE_URL` dans `.env.local`).
- Application : http://projectshoppinglist.dev.localhost:8080 (vhost `*.dev.localhost` → `/var/www/html/<sous-domaine>/public`).
- PHP n'est pas installé sur l'hôte : lancer les commandes PHP avec `docker exec -w /var/www/html/projectshoppinglist php-8.0.23 php bin/console …`. De même pour `composer` et `npm` (Node 14 est dans le conteneur, pas sur l'hôte), ex. `docker exec -w /var/www/html/projectshoppinglist php-8.0.23 npm run dev`.

## Commandes

```bash
composer install                                # chmod +x le binaire wkhtmltopdf en post-install
npm install
npm run dev | npm run watch | npm run build     # Encore -> public/build/

php bin/console doctrine:migrations:migrate
php bin/console make:migration                  # après une modification d'entité
php bin/console cache:clear

symfony serve   # ou: php -S localhost:8000 -t public

php bin/phpunit                                 # tests (dossier tests/ quasi vide pour l'instant)
php bin/phpunit --filter NomDuTest
```

Si la génération PDF échoue sur une erreur de permission : `./fix-wkhtmltopdf-permissions.sh`.

## Architecture

**Multi-utilisateur par propriété.** `Recipe`, `Ingredient` et `ShoppingList` ont chacun un `ManyToOne` non nullable vers `User`. Le cloisonnement se fait à la main :
- les actions `index` filtrent avec `findBy(['user' => $this->getUser()])` et les actions `new` appellent `setUser($this->getUser())` ;
- les formulaires qui proposent des entités liées (`ShoppingListType` → recettes, `RecipeIngredientType` → ingrédients) injectent `TokenStorageInterface` et restreignent leur `query_builder` à l'utilisateur courant.
- les routes `{id}` commencent par `$this->denyAccessUnlessGranted(OwnerVoter::OWNER, $entity)` (`src/Security/Voter/OwnerVoter.php`, qui gère `Recipe`, `Ingredient` et `ShoppingList`). Toute nouvelle route `{id}` doit faire de même. Toute nouvelle entité possédée par un utilisateur doit être ajoutée à `OwnerVoter::supports()`.
Les fonctions Twig qui comptent ou listent des données (ex. `RecipeExtension`) doivent aussi filtrer par utilisateur courant (service `Security`).

**Sécurité** (`config/packages/security.yaml`) : form login sur l'email, remember-me toujours actif (30 jours) ; tout est `ROLE_USER` sauf `/login` et `/register`.

**Modèle de liste de courses** — le cœur métier, réparti sur plusieurs entités :
- `Recipe` ↔ `Ingredient` via l'entité pivot `RecipeIngredient` (porte la quantité ; l'unité est sur `Ingredient`).
- `ShoppingList` ManyToMany `Recipe`, plus `customItems` (`ShoppingListItem` : articles libres hors recette, avec `isInCart`) et `ingredientAdjustments` (`ShoppingListIngredientAdjustment`, unique par couple liste/ingrédient : `adjustedQuantity`, `isHidden`, `isInCart`).
- Les totaux ne sont **pas stockés** : `ShoppingList::getTotalIngredients()` les recalcule à chaque appel en cumulant les quantités des recettes par ingrédient, puis applique les ajustements. Il renvoie des tableaux (`ingredient`, `quantity`, `unit`, `isHidden`, `adjustedQuantity`, `inCart`) consommés par `shopping_list/show.html.twig` et `shopping_list/pdf.html.twig`.
- `ShoppingListController::adjustIngredient` (POST JSON sur `/shopping/list/{id}/adjust-ingredient`) est appelé en AJAX par le JavaScript inline de `show.html.twig`. Il gère soit un ingrédient (`ingredientId` : crée/maj l'ajustement, et le supprime s'il redevient neutre), soit un article libre (`customItemId` : seulement `inCart`). Détails dans `INGREDIENT_ADJUSTMENT_GUIDE.md`.

**Doublons d'ingrédients** : `Ingredient::normalizeName()` (sans accents, minuscules, pluriel s/x retiré) est la seule définition d'un doublon. Elle sert au blocage côté serveur (`IngredientController::rejectDuplicate`), à l'avertissement en direct (`GET /ingredient/check-duplicate`, appelé depuis `ingredient/_form.html.twig`), à la création rapide depuis une recette (`POST /ingredient/quick-create`, modale de `recipe/_form.html.twig`, renvoie 409 avec l'ingrédient existant) et au badge « Doublon » de la liste. Les unités autorisées sont dans `IngredientType::UNITS`.

**PDF** : KnpSnappy + binaire `h4cc/wkhtmltopdf-amd64` embarqué dans `vendor/` (`config/packages/knp_snappy.yaml`). Le template `shopping_list/pdf.html.twig` doit avoir des styles autonomes (wkhtmltopdf ne charge pas les assets Encore).

**Divers Doctrine** :
- Mapping par attributs PHP 8 ; `TimestampableEntity` de Gedmo (`config/packages/doctrine_extensions.yaml`) ajoute `createdAt`/`updatedAt`.
- `RecipeListener` (déclaré dans `config/services.yaml` comme entity listener `prePersist`) initialise `Recipe::numberOfTimes` à 0 — compteur incrémenté par `RecipeController` route `mark-as-done` (voir `COMPTEUR_RECETTES.md`).
- Supprimer un `Ingredient` le retire de toutes les recettes (cascade) ; supprimer une liste n'affecte pas les recettes (voir `SUPPRESSION_GUIDE.md`).

**Front** : entrée unique `assets/app.js` ; contrôleurs Stimulus dans `assets/controllers/` (collections de formulaires recette/ingrédients) ; l'unité d'un ingrédient est récupérée dynamiquement via `GET /ingredient/{id}/api`. Styles SCSS dans `assets/styles/` ; les règles mobiles et tactiles sont dans `_mobile.scss`, importé après Bootstrap. Dans `base.html.twig`, `.main-column` doit garder `min-width: 0`, sinon les tableaux élargissent la page sur mobile. Sous md, le contenu a 12 px de marge (une demi-gouttière) pour absorber les marges négatives des `.row`. Ne pas placer un `.row` directement dans un `.d-grid` : utiliser `.grid-2-cols`. En dev, `public/build/app.css` n'est pas versionné : forcer le rechargement après `npm run dev`. Une bonne partie du JS spécifique aux pages est écrite inline dans les templates Twig.

## Documentation existante

Les guides à la racine (`PDF_GUIDE.md`, `RESPONSIVE_GUIDE.md`, `AUTHENTICATION_GUIDE.md`, `SUPPRESSION_GUIDE.md`, `COMPTEUR_RECETTES.md`, `INGREDIENT_ADJUSTMENT_GUIDE.md`…) décrivent chaque fonctionnalité ; les consulter avant de modifier la zone concernée. `CREDENTIALS.md` contient des identifiants : ne pas recopier son contenu ailleurs.

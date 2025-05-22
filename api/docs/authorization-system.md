# Système d'Autorisations GESTISTOCK

Ce document décrit le système d'autorisations implémenté dans l'API GESTISTOCK.

## Rôles Utilisateurs

Le système GESTISTOCK définit deux rôles principaux :

1. **Admin** - Administrateurs du système avec accès complet
2. **Magasinier** - Utilisateurs standard avec accès limité

### Permissions par Rôle

#### Administrateur (`admin`)

Les administrateurs ont accès à toutes les fonctionnalités du système :

- Consultation de toutes les données
- Création, modification et suppression de tous les types d'entités
- Gestion des utilisateurs (création, modification, activation/désactivation)
- Configuration du système
- Gestion complète des catégories et fournisseurs

#### Magasinier (`magasinier`)

Les magasiniers ont un accès limité, principalement orienté vers les opérations quotidiennes :

- Consultation de toutes les données (produits, catégories, fournisseurs, mouvements de stocks, bons d'entrée et de sortie)
- Création de mouvements de stock
- Création de bons d'entrée et de sortie
- Pas d'accès à la gestion des utilisateurs
- Pas d'accès à la modification/suppression des catégories et fournisseurs

## Middlewares d'Autorisation

### CheckRole Middleware

Le middleware `CheckRole` vérifie si l'utilisateur connecté possède le rôle requis pour accéder à une ressource spécifique.

#### Utilisation dans les routes

```php
// Route accessible uniquement aux administrateurs
Route::get('admin-resource', [Controller::class, 'method'])->middleware('role:admin');

// Route accessible aux administrateurs et magasiniers
Route::get('shared-resource', [Controller::class, 'method'])->middleware('role:admin,magasinier');
```

### Protection des Routes

Les routes sont protégées à plusieurs niveaux :

1. **Authentification** - Toutes les routes API (sauf login) nécessitent une authentification via JWT
2. **Autorisation par Rôle** - Les routes sont regroupées par niveau d'accès avec le middleware `role`

## Test des Autorisations

Pour tester le système d'autorisations, vous pouvez utiliser les routes suivantes :

- `GET /api/test-roles` - Affiche les informations de l'utilisateur connecté, y compris son rôle
- `GET /api/admin-only` - Route de test accessible uniquement aux administrateurs
- `GET /api/magasinier-only` - Route de test accessible uniquement aux magasiniers
- `GET /api/admin-ou-magasinier` - Route de test accessible aux deux rôles

## Bonnes Pratiques

1. Toujours vérifier les autorisations au niveau des routes ET des contrôleurs
2. Ne jamais exposer de routes sans protection d'authentification
3. Utiliser le middleware `role` pour limiter l'accès aux fonctionnalités sensibles
4. Vérifier les permissions pour les opérations CRUD dans les contrôleurs

# PRD: Backend Laravel API pour GESTISTOCK

## 1. Vue d'ensemble du projet

GESTISTOCK est une application de gestion de stock complète qui permet aux entreprises de gérer leur inventaire, suivre les mouvements de stock, gérer les fournisseurs et générer des rapports. Le frontend a déjà été développé en Next.js avec une interface utilisateur moderne et réactive. Ce document définit les exigences pour le développement d'un backend Laravel API qui s'intégrera parfaitement avec le frontend existant.

## 2. Objectifs et portée

### Objectifs
- Développer une API RESTful avec Laravel pour servir le frontend GESTISTOCK
- Implémenter toutes les fonctionnalités nécessaires pour prendre en charge les opérations du frontend
- Assurer une sécurité robuste avec authentification JWT
- Fournir une documentation API complète
- Permettre une installation et configuration faciles dans un environnement XAMPP

### Portée
Le backend doit prendre en charge toutes les fonctionnalités du frontend, notamment:
- Gestion des produits et catégories
- Gestion des stocks et mouvements
- Gestion des fournisseurs
- Gestion des bons d'entrée et de sortie
- Gestion des utilisateurs et des rôles
- Génération de rapports et analyses

## 3. Architecture technique

### Stack technologique
- **Framework**: Laravel 10+
- **Base de données**: MySQL 8.0+ (via phpMyAdmin)
- **Environnement**: XAMPP
- **Authentification**: JWT (JSON Web Tokens)
- **Documentation API**: Swagger/OpenAPI

### Structure du projet
\`\`\`
gestistock-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/
│   └── Repositories/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
└── ...
\`\`\`

## 4. Modèles de données

### Tables de base de données

#### users
- id (PK)
- name
- email (unique)
- password
- role (enum: 'admin', 'magasinier')
- active (boolean)
- created_at
- updated_at

#### categories
- id (PK)
- name (unique)
- created_at
- updated_at

#### products
- id (PK)
- reference (unique)
- name
- description (nullable)
- category_id (FK)
- price (decimal)
- quantity (integer)
- min_stock (integer)
- created_at
- updated_at

#### stock_movements
- id (PK)
- product_id (FK)
- type (enum: 'entry', 'exit')
- quantity (integer)
- reason (text)
- date (timestamp)
- user_id (FK)
- created_at
- updated_at

#### suppliers
- id (PK)
- name
- email
- phone
- address
- notes (nullable)
- created_at
- updated_at

#### entry_forms
- id (PK)
- reference (unique)
- date (date)
- supplier_id (FK)
- notes (nullable)
- status (enum: 'draft', 'pending', 'completed')
- total (decimal)
- user_id (FK)
- created_at
- updated_at

#### entry_items
- id (PK)
- entry_form_id (FK)
- product_id (FK)
- quantity (integer)
- unit_price (decimal)
- total (decimal)
- created_at
- updated_at

#### exit_forms
- id (PK)
- reference (unique)
- date (date)
- destination (string)
- reason (string)
- notes (nullable)
- status (enum: 'draft', 'pending', 'completed')
- user_id (FK)
- created_at
- updated_at

#### exit_items
- id (PK)
- exit_form_id (FK)
- product_id (FK)
- quantity (integer)
- created_at
- updated_at

## 5. API Endpoints

### Authentification
- `POST /api/auth/login` - Connexion utilisateur
- `POST /api/auth/logout` - Déconnexion utilisateur
- `GET /api/auth/user` - Obtenir l'utilisateur connecté

### Produits
- `GET /api/products` - Liste des produits (avec filtres et pagination)
- `GET /api/products/{id}` - Détails d'un produit
- `POST /api/products` - Créer un produit
- `PUT /api/products/{id}` - Mettre à jour un produit
- `DELETE /api/products/{id}` - Supprimer un produit
- `GET /api/products/low-stock` - Produits en stock faible

### Catégories
- `GET /api/categories` - Liste des catégories
- `GET /api/categories/{id}` - Détails d'une catégorie
- `POST /api/categories` - Créer une catégorie
- `PUT /api/categories/{id}` - Mettre à jour une catégorie
- `DELETE /api/categories/{id}` - Supprimer une catégorie
- `GET /api/categories/{id}/products` - Produits d'une catégorie

### Mouvements de stock
- `GET /api/stock-movements` - Liste des mouvements (avec filtres et pagination)
- `POST /api/stock-movements` - Créer un mouvement de stock
- `GET /api/stock-movements/product/{productId}` - Mouvements d'un produit

### Fournisseurs
- `GET /api/suppliers` - Liste des fournisseurs
- `GET /api/suppliers/{id}` - Détails d'un fournisseur
- `POST /api/suppliers` - Créer un fournisseur
- `PUT /api/suppliers/{id}` - Mettre à jour un fournisseur
- `DELETE /api/suppliers/{id}` - Supprimer un fournisseur
- `GET /api/suppliers/{id}/products` - Produits d'un fournisseur

### Bons d'entrée
- `GET /api/entry-forms` - Liste des bons d'entrée
- `GET /api/entry-forms/{id}` - Détails d'un bon d'entrée
- `POST /api/entry-forms` - Créer un bon d'entrée
- `PUT /api/entry-forms/{id}` - Mettre à jour un bon d'entrée
- `DELETE /api/entry-forms/{id}` - Supprimer un bon d'entrée

### Bons de sortie
- `GET /api/exit-forms` - Liste des bons de sortie
- `GET /api/exit-forms/{id}` - Détails d'un bon de sortie
- `POST /api/exit-forms` - Créer un bon de sortie
- `PUT /api/exit-forms/{id}` - Mettre à jour un bon de sortie
- `DELETE /api/exit-forms/{id}` - Supprimer un bon de sortie

### Utilisateurs
- `GET /api/users` - Liste des utilisateurs (admin seulement)
- `GET /api/users/{id}` - Détails d'un utilisateur
- `POST /api/users` - Créer un utilisateur (admin seulement)
- `PUT /api/users/{id}` - Mettre à jour un utilisateur
- `DELETE /api/users/{id}` - Supprimer un utilisateur (admin seulement)

### Rapports et Analyses
- `GET /api/reports/inventory` - Rapport d'inventaire actuel
- `GET /api/reports/movements` - Rapport des mouvements
- `GET /api/reports/valuation` - Rapport de valorisation du stock
- `GET /api/reports/turnover` - Rapport de rotation des produits
- `GET /api/analytics/dashboard` - Données pour le tableau de bord
- `GET /api/analytics/stock-evolution` - Évolution des stocks
- `GET /api/analytics/category-distribution` - Distribution par catégorie
- `GET /api/analytics/top-products` - Produits les plus utilisés

## 6. Authentification et Sécurité

### Authentification
- Utiliser JWT (JSON Web Tokens) pour l'authentification
- Implémenter la gestion des tokens avec expiration et rafraîchissement
- Stocker les mots de passe avec hachage bcrypt

### Autorisations
- Implémenter un système de contrôle d'accès basé sur les rôles
- Rôle "admin": accès complet à toutes les fonctionnalités
- Rôle "magasinier": accès limité (pas d'accès à la gestion des utilisateurs, catégories, etc.)

### Sécurité
- Implémenter CORS pour permettre les requêtes du frontend
- Valider toutes les entrées utilisateur
- Protéger contre les attaques CSRF
- Journaliser les actions importantes

## 7. Exigences techniques

### Performance
- Optimiser les requêtes de base de données avec des index appropriés
- Implémenter la mise en cache pour les requêtes fréquentes
- Paginer les résultats pour les grandes collections de données

### Validation
- Valider toutes les entrées utilisateur côté serveur
- Renvoyer des messages d'erreur clairs et informatifs

### Gestion des erreurs
- Implémenter une gestion d'erreurs cohérente
- Journaliser les erreurs pour le débogage
- Renvoyer des codes d'état HTTP appropriés

## 8. Instructions d'installation et de configuration

### Prérequis
- XAMPP installé (PHP 8.1+, MySQL, Apache)
- Composer
- Git

### Installation
1. Cloner le dépôt: `git clone [URL_DU_REPO] gestistock-api`
2. Naviguer vers le répertoire: `cd gestistock-api`
3. Installer les dépendances: `composer install`
4. Copier le fichier d'environnement: `cp .env.example .env`
5. Configurer les variables d'environnement dans `.env`:
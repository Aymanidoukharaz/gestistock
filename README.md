# GESTISTOCK - Système de Gestion de Stock

## Description du projet

GESTISTOCK est un système complet de gestion de stock qui permet de suivre l'inventaire, les mouvements de stock, les entrées et les sorties de produits. Le système est composé d'une API backend développée avec Laravel 10 et d'une interface utilisateur frontend développée avec Next.js.

## Structure du projet

Le projet est organisé en deux parties principales :

- **api/** : Backend Laravel 10 avec API RESTful
- **frontend/** : Frontend Next.js pour l'interface utilisateur

## Fonctionnalités principales

- Authentification JWT sécurisée
- Gestion des produits, catégories et fournisseurs
- Gestion des entrées et sorties de stock
- Suivi des mouvements de stock
- Tableau de bord avec statistiques et rapports
- Interface utilisateur responsive et moderne

## Installation

### Prérequis

- PHP 8.1 ou supérieur
- Composer
- MySQL ou MariaDB
- Node.js et npm/pnpm

### Installation du Backend (Laravel)

```bash
# Se positionner dans le répertoire api
cd api

# Installer les dépendances
composer install

# Copier le fichier d'environnement et configurer les variables
cp .env.example .env

# Générer une clé d'application
php artisan key:generate

# Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=gestistock
# DB_USERNAME=root
# DB_PASSWORD=

# Générer la clé JWT
php artisan jwt:secret

# Exécuter les migrations et les seeders
php artisan migrate --seed

# Lancer le serveur de développement
php artisan serve
```

### Installation du Frontend (Next.js)

```bash
# Se positionner dans le répertoire frontend
cd frontend

# Installer les dépendances
npm install
# ou avec pnpm
pnpm install

# Copier le fichier d'environnement
cp .env.example .env.local

# Configurer l'URL de l'API dans .env.local
# NEXT_PUBLIC_API_URL=http://localhost:8000/api

# Lancer le serveur de développement
npm run dev
# ou avec pnpm
pnpm dev
```

## Structure des API

Le backend expose des API RESTful pour toutes les entités principales :

- `/api/auth` : Authentification (login, register, refresh, logout)
- `/api/products` : Gestion des produits
- `/api/categories` : Gestion des catégories
- `/api/suppliers` : Gestion des fournisseurs
- `/api/stock-movements` : Gestion des mouvements de stock
- `/api/entry-forms` : Gestion des bons d'entrée
- `/api/exit-forms` : Gestion des bons de sortie

## Phase de développement actuelle

Le projet est actuellement en phase de développement. La phase 7 (Classes de requête pour validation) est en cours d'implémentation.

## Contribuer

Pour contribuer au projet, veuillez créer une branche pour vos fonctionnalités et soumettre une pull request.

## Licence

Ce projet est sous licence [MIT](LICENSE).

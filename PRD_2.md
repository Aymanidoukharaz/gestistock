# Document de Spécifications Produit (DSP) - Application de Gestion de Stock

## 1. Introduction

Ce document décrit les spécifications produit pour l'Application de Gestion de Stock, probablement nommée "Gestistock". L'objectif principal de cette application est de fournir un système complet pour la gestion des stocks, le suivi des mouvements de produits (entrées et sorties), et la tenue des registres des fournisseurs, des catégories de produits et des utilisateurs. Elle vise à rationaliser les opérations de stock et à fournir des informations précieuses grâce à des analyses de tableau de bord.

## 2. Fonctionnalités Clés

L'application offre les fonctionnalités principales suivantes :

*   **Authentification et Autorisation des Utilisateurs :**
    *   Connexion et déconnexion sécurisées des utilisateurs.
    *   Contrôle d'accès basé sur les rôles, distinguant les rôles "Admin" et "Magasinier".
    *   Les utilisateurs administrateurs ont la possibilité d'enregistrer de nouveaux utilisateurs.
*   **Gestion des Produits :**
    *   Capacités CRUD complètes (Créer, Lire, Mettre à jour, Supprimer) pour les enregistrements de produits.
    *   Chaque produit comprend des attributs tels que la référence, le nom, la description, la catégorie associée, le prix, la quantité actuelle en stock et le niveau de stock minimum.
*   **Gestion des Catégories :**
    *   Opérations CRUD complètes pour les catégories de produits, permettant l'organisation et la classification des produits.
*   **Gestion des Fournisseurs :**
    *   Opérations CRUD complètes pour les informations sur les fournisseurs, y compris le nom, les coordonnées (e-mail, téléphone, adresse) et les notes facultatives.
*   **Bons d'Entrée de Stock :**
    *   Création et gestion des formulaires pour les stocks entrants.
    *   Les formulaires comprennent une référence, une date, un fournisseur associé, des notes, un statut (brouillon, en attente, terminé) et une liste d'articles d'entrée.
    *   Chaque article d'entrée spécifie le produit, la quantité et le prix unitaire.
    *   Fonctionnalité pour valider/compléter et annuler les bons d'entrée.
    *   Possibilité de vérifier les doublons de bons d'entrée et de consulter l'historique des formulaires.
*   **Bons de Sortie de Stock :**
    *   Création et gestion des formulaires pour les stocks sortants.
    *   Les formulaires comprennent une référence, une date, une destination, une raison, des notes, un statut (brouillon, en attente, terminé) et une liste d'articles de sortie.
    *   Chaque article de sortie spécifie le produit et la quantité.
    *   Fonctionnalité pour valider/compléter et annuler les bons de sortie.
    *   Possibilité de vérifier les doublons de bons de sortie et de consulter l'historique des formulaires.
*   **Suivi des Mouvements de Stock :**
    *   Enregistre les mouvements de stock individuels (entrées et sorties) pour chaque produit.
    *   Chaque mouvement comprend le produit, le type (entrée/sortie), la quantité, la date et la raison.
*   **Tableau de Bord et Rapports :**
    *   Fournit un aperçu analytique des données de stock.
    *   Comprend un résumé du stock actuel, des mouvements de stock récents, une analyse du stock par catégorie et des graphiques de tendances des mouvements de stock.
*   **Gestion des Utilisateurs (Admin Uniquement) :**
    *   Les administrateurs peuvent consulter, créer, mettre à jour, activer/désactiver et supprimer des comptes d'utilisateurs.

## 3. Architecture

L'application suit une **Architecture Client-Serveur** avec une séparation claire des préoccupations entre les composants frontend et backend.

*   **Frontend (Côté client) :** Il s'agit d'une application monopage (SPA) construite avec Next.js (React). Elle est responsable du rendu de l'interface utilisateur, de la gestion des interactions utilisateur et de l'envoi de requêtes API au backend. Elle gère le routage côté client et la présentation des données.
*   **Backend (Côté serveur) :** Il s'agit d'une API RESTful développée à l'aide du framework Laravel (PHP). Elle gère toute la logique métier, la validation des données, les interactions avec la base de données et sert les données au frontend. Elle gère également l'authentification et l'autorisation des utilisateurs.
*   **Flux d'Interaction :**
    1.  Les utilisateurs accèdent à l'application via le frontend Next.js via un navigateur web.
    2.  Le frontend envoie des requêtes HTTP/HTTPS (GET, POST, PUT, DELETE) à des points de terminaison API spécifiques exposés par le backend Laravel.
    3.  L'API Laravel traite ces requêtes, interagit avec la base de données sous-jacente (par exemple, MySQL, PostgreSQL), applique les règles métier nécessaires (par exemple, mise à jour des quantités de stock, validation des soumissions de formulaires) et récupère ou stocke les données.
    4.  L'API renvoie ensuite des réponses JSON au frontend.
    5.  Le frontend reçoit ces réponses JSON et met à jour dynamiquement l'interface utilisateur pour refléter les changements ou afficher les informations demandées.
    6.  L'authentification est gérée à l'aide de JSON Web Tokens (JWT). Après une connexion réussie, le frontend reçoit un JWT du backend et l'inclut dans les en-têtes des requêtes API protégées ultérieures.

```mermaid
graph TD
    User -->|Interagit avec| Frontend[Frontend (Application Next.js)]
    Frontend -->|Requêtes HTTP/HTTPS (API RESTful)| Backend[Backend (API Laravel)]
    Backend -->|Opérations de Base de Données| Database[(Base de Données)]
    Backend -->|Réponses JSON| Frontend
```

## 4. Pile Technologique

### Frontend

*   **Framework :** Next.js (React)
*   **Bibliothèque UI/Composants :** Radix UI, Shadcn UI (implicite par la structure des composants)
*   **Style :** Tailwind CSS, Autoprefixer, PostCSS
*   **Gestion des Formulaires :** React Hook Form, Zod (pour la validation de schéma)
*   **Client HTTP :** Axios
*   **Graphiques :** Recharts
*   **Génération de PDF :** html2canvas, jspdf, jspdf-autotable
*   **Autres Bibliothèques :**
    *   `lucide-react` : Bibliothèque d'icônes.
    *   `date-fns` : Bibliothèque utilitaire de dates.
    *   `next-themes` : Gestion des thèmes.
    *   `sonner` : Notifications toast.
    *   `uuid` : Génération d'ID uniques.
    *   `react-resizable-panels` : Composants de panneaux redimensionnables.
    *   `cmdk` : Palette de commandes.
    *   `input-otp` : Saisie de mot de passe à usage unique.
    *   `vaul` : Boîtes de dialogue/tiroirs.
    *   `clsx`, `tailwind-merge`, `class-variance-authority` : Utilitaires pour le style conditionnel.
    *   `@types/node`, `@types/react`, `@types/react-dom`, `typescript` : Dépendances de développement pour la sécurité de type.

### Backend

*   **Framework :** Laravel (PHP)
*   **Authentification :** Tymon JWT Auth
*   **Base de Données :** (Généralement MySQL ou PostgreSQL avec Laravel, inféré)
*   **Outils de Développement :**
    *   `php` : PHP 8.2+
    *   `laravel/tinker` : Interaction en ligne de commande avec les applications Laravel.
    *   `fakerphp/faker` : Génération de données pour les tests.
    *   `laravel/pail` : Suivi des journaux en temps réel.
    *   `laravel/pint` : Correcteur de style de code PHP.
    *   `laravel/sail` : Environnement de développement Docker pour Laravel.
    *   `mockery/mockery` : Framework d'objets simulés pour les tests.
    *   `nunomaduro/collision` : Rapports d'erreurs élégants pour les applications console.
    *   `phpunit/phpunit` : Framework de test PHP.

## 5. Modèles de Données (Inférés)

Basé sur les définitions de type TypeScript dans `frontend/types/` et la structure générale de l'API, les principales entités de données sont :

*   **Category (Catégorie) :**
    *   `id` : string (Identifiant unique)
    *   `name` : string (Nom de la catégorie)
    *   `description` : string | null (Description, peut être nulle)
    *   `created_at` : string (Chaîne de date ISO 8601)
    *   `updated_at` : string (Chaîne de date ISO 8601)
    *   `products_count?` : number (Optionnel, probablement un champ calculé)
*   **Product (Produit) :**
    *   `id` : string (Identifiant unique)
    *   `reference` : string (Référence du produit)
    *   `name` : string (Nom du produit)
    *   `description` : string (Description du produit)
    *   `category` : Category (Objet Catégorie imbriqué)
    *   `price` : number (Prix du produit)
    *   `quantity` : number (Quantité actuelle en stock)
    *   `min_stock` : number (Seuil de stock minimum)
*   **Supplier (Fournisseur) :**
    *   `id` : string (Identifiant unique)
    *   `name` : string (Nom du fournisseur)
    *   `email` : string (Adresse e-mail du fournisseur)
    *   `phone` : string (Numéro de téléphone du fournisseur)
    *   `address` : string (Adresse du fournisseur)
    *   `notes?` : string (Notes facultatives)
*   **User (Utilisateur) :**
    *   `id` : number (Identifiant unique)
    *   `name` : string (Nom de l'utilisateur)
    *   `email` : string (Adresse e-mail de l'utilisateur)
    *   `role` : "admin" | "magasinier" (Rôle de l'utilisateur)
    *   `active` : boolean (Indique si le compte utilisateur est actif)
    *   `password?` : string (Optionnel, utilisé pour la création/mise à jour)
*   **EntryForm (Bon d'Entrée) :**
    *   `id` : string (Identifiant unique)
    *   `reference` : string (Référence du bon d'entrée)
    *   `date` : string (Chaîne de date ISO 8601)
    *   `supplierId` : string (ID du fournisseur associé)
    *   `supplierName` : string (Nom du fournisseur associé)
    *   `notes` : string (Notes)
    *   `status` : "draft" | "pending" | "completed" (Statut du bon d'entrée)
    *   `items` : EntryItem[] (Tableau d'articles d'entrée associés)
    *   `total` : number (Valeur totale du bon d'entrée)
*   **EntryItem (Article d'Entrée) :**
    *   `id` : string (Identifiant unique)
    *   `productId` : string (ID du produit dans l'entrée)
    *   `productName` : string (Nom du produit dans l'entrée)
    *   `quantity` : number (Quantité du produit entrée)
    *   `unitPrice` : number (Prix unitaire du produit à l'entrée)
    *   `total` : number (Prix total pour cet article)
*   **ExitForm (Bon de Sortie) :**
    *   `id` : string (Identifiant unique)
    *   `reference` : string (Référence du bon de sortie)
    *   `date` : string (Chaîne de date ISO 8601)
    *   `destination` : string (Destination de la sortie)
    *   `reason` : string (Raison de la sortie)
    *   `notes` : string (Notes)
    *   `status` : "draft" | "pending" | "completed" (Statut du bon de sortie)
    *   `items` : ExitItem[] (Tableau d'articles de sortie associés)
    *   `total?` : number (Optionnel, valeur totale du bon de sortie)
*   **ExitItem (Article de Sortie) :**
    *   `id` : string (Identifiant unique)
    *   `product` : Product (Objet Produit imbriqué)
    *   `quantity` : number (Quantité du produit sorti)
*   **StockMovement (Mouvement de Stock) :**
    *   `id` : string (Identifiant unique)
    *   `product` : Product (Objet Produit imbriqué)
    *   `type` : "entry" | "exit" (Type de mouvement : entrée ou sortie)
    *   `quantity` : number (Quantité du mouvement)
    *   `date` : string (Chaîne de date ISO 8601)
    *   `reason` : string (Raison du mouvement)
    *   `created_at` : string (Chaîne de date ISO 8601)
    *   `updated_at` : string (Chaîne de date ISO 8601)

## 6. Points de Terminaison API (Inférés)

Voici les principaux points de terminaison API, inférés des appels de service frontend et des routes backend, ainsi que leurs objectifs et méthodes HTTP typiques. L'accès basé sur les rôles est indiqué le cas échéant.

*   **Authentification :**
    *   `POST /auth/login` : Authentifier un utilisateur.
    *   `POST /auth/logout` : Déconnecter l'utilisateur authentifié (Protégé).
    *   `GET /auth/user` : Obtenir les détails de l'utilisateur authentifié (Protégé).
    *   `POST /auth/register` : Enregistrer un nouvel utilisateur (Protégé, Admin uniquement).
*   **Produits :**
    *   `GET /products` : Récupérer une liste de tous les produits (Protégé, Admin/Magasinier).
    *   `GET /products/{product}` : Récupérer les détails d'un produit spécifique (Protégé, Admin/Magasinier).
    *   `POST /products` : Créer un nouveau produit (Protégé, Admin uniquement).
    *   `PUT /products/{product}` : Mettre à jour un produit existant (Protégé, Admin uniquement).
    *   `DELETE /products/{product}` : Supprimer un produit (Protégé, Admin uniquement).
    *   `GET /products/categories` : Récupérer une liste de catégories de produits (Protégé, Admin/Magasinier).
*   **Catégories :**
    *   `GET /categories` : Récupérer une liste de toutes les catégories (Protégé, Admin uniquement).
    *   `GET /categories/{category}` : Récupérer les détails d'une catégorie spécifique (Protégé, Admin uniquement).
    *   `POST /categories` : Créer une nouvelle catégorie (Protégé, Admin uniquement).
    *   `PUT /categories/{category}` : Mettre à jour une catégorie existante (Protégé, Admin uniquement).
    *   `DELETE /categories/{category}` : Supprimer une catégorie (Protégé, Admin uniquement).
*   **Fournisseurs :**
    *   `GET /suppliers` : Récupérer une liste de tous les fournisseurs (Protégé, Admin uniquement).
    *   `GET /suppliers/{supplier}` : Récupérer les détails d'un fournisseur spécifique (Protégé, Admin uniquement).
    *   `POST /suppliers` : Créer un nouveau fournisseur (Protégé, Admin uniquement).
    *   `PUT /suppliers/{supplier}` : Mettre à jour un fournisseur existant (Protégé, Admin uniquement).
    *   `DELETE /suppliers/{supplier}` : Supprimer un fournisseur (Protégé, Admin uniquement).
*   **Mouvements de Stock :**
    *   `GET /stock-movements` : Récupérer une liste de tous les mouvements de stock (Protégé, Admin/Magasinier).
    *   `GET /stock-movements/{stockMovement}` : Récupérer les détails d'un mouvement de stock spécifique (Protégé, Admin/Magasinier).
    *   `POST /stock-movements` : Créer un nouveau mouvement de stock (Protégé, Admin/Magasinier).
    *   `PUT /stock-movements/{stockMovement}` : Mettre à jour un mouvement de stock existant (Protégé, Admin uniquement).
    *   `DELETE /stock-movements/{stockMovement}` : Supprimer un mouvement de stock (Protégé, Admin uniquement).
    *   `GET /stock-movements/product/{product}` : Récupérer les mouvements de stock pour un produit spécifique (Protégé, Admin/Magasinier).
*   **Bons d'Entrée :**
    *   `GET /entry-forms` : Récupérer une liste de tous les bons d'entrée (Protégé, Admin/Magasinier).
    *   `GET /entry-forms/{entryForm}` : Récupérer les détails d'un bon d'entrée spécifique (Protégé, Admin/Magasinier).
    *   `POST /entry-forms` : Créer un nouveau bon d'entrée (Protégé, Admin/Magasinier).
    *   `PUT /entry-forms/{entryForm}` : Mettre à jour un bon d'entrée existant (Protégé, Admin uniquement).
    *   `DELETE /entry-forms/{entryForm}` : Supprimer un bon d'entrée (Protégé, Admin uniquement).
    *   `POST /entry-forms/{entryForm}/validate` : Valider/compléter un bon d'entrée (Protégé, Admin/Magasinier).
    *   `POST /entry-forms/{entryForm}/cancel` : Annuler un bon d'entrée (Protégé, Admin uniquement).
    *   `GET /entry-forms/{entryForm}/history` : Obtenir l'historique d'un bon d'entrée (Protégé, Admin/Magasinier).
    *   `POST /entry-forms/check-duplicates` : Vérifier les doublons de bons d'entrée (Protégé, Admin/Magasinier).
*   `GET /entry-forms/{entryForm}/debug` : Obtenir les informations de débogage pour un bon d'entrée spécifique (Protégé, Admin/Magasinier).
*   **Bons de Sortie :**
    *   `GET /exit-forms` : Récupérer une liste de tous les bons de sortie (Protégé, Admin/Magasinier).
    *   `GET /exit-forms/{exitForm}` : Récupérer les détails d'un bon de sortie spécifique (Protégé, Admin/Magasinier).
    *   `POST /exit-forms` : Créer un nouveau bon de sortie (Protégé, Admin/Magasinier).
    *   `PUT /exit-forms/{exitForm}` : Mettre à jour un bon de sortie existant (Protégé, Admin uniquement).
    *   `DELETE /exit-forms/{exitForm}` : Supprimer un bon de sortie (Protégé, Admin uniquement).
    *   `POST /exit-forms/{exitForm}/validate` : Valider/compléter un bon de sortie (Protégé, Admin/Magasinier).
    *   `POST /exit-forms/{exitForm}/cancel` : Annuler un bon de sortie (Protégé, Admin uniquement).
    *   `GET /exit-forms/{exitForm}/history` : Obtenir l'historique d'un bon de sortie (Protégé, Admin/Magasinier).
    *   `POST /exit-forms/check-duplicates` : Vérifier les doublons de bons de sortie (Protégé, Admin/Magasinier).
*   **Tableau de Bord :**
    *   `GET /dashboard/summary` : Obtenir les données récapitulatives pour le tableau de bord (Protégé, Admin/Magasinier).
    *   `GET /dashboard/recent-movements` : Obtenir les mouvements de stock récents pour le tableau de bord (Protégé, Admin/Magasinier).
    *   `GET /dashboard/category-analysis` : Obtenir les données d'analyse de stock par catégorie pour le tableau de bord (Protégé, Admin/Magasinier).
    *   `GET /dashboard/stock-movement-chart` : Obtenir les données pour les graphiques de mouvements de stock (Protégé, Admin/Magasinier).
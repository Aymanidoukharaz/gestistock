# Plan de test des API - Phase 8

Ce document contient le plan de test complet pour valider la Phase 8 du projet GESTISTOCK - Standardisation des réponses API.

## Prérequis

- Serveur Laravel démarré (`php artisan serve`)
- Base de données configurée et accessible
- Postman ou un outil similaire pour les tests d'API

## Configuration pour tous les tests

Pour chaque requête API protégée, incluez l'en-tête d'autorisation :
```
Authorization: Bearer votre_token_jwt
```

## 1. Tests d'authentification

### 1.1 Login (Connexion)

- **Méthode** : POST
- **URL** : http://localhost:8000/api/auth/login
- **Corps** :
  ```json
  {
    "email": "admin@example.com",
    "password": "password"
  }
  ```
- **Réponse attendue** :
  ```json
  {
    "success": true,
    "message": "Authentification réussie",
    "data": {
      "access_token": "token_jwt",
      "token_type": "bearer",
      "expires_in": 3600,
      "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com",
        "role": "admin"
      }
    }
  }
  ```

### 1.2 Récupération des informations de l'utilisateur courant

- **Méthode** : GET
- **URL** : http://localhost:8000/api/auth/user
- **Réponse attendue** :
  ```json
  {
    "success": true,
    "message": "Informations utilisateur récupérées avec succès",
    "data": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "role": "admin"
    }
  }
  ```

### 1.3 Déconnexion

- **Méthode** : POST
- **URL** : http://localhost:8000/api/auth/logout
- **Réponse attendue** :
  ```json
  {
    "success": true,
    "message": "Déconnecté avec succès",
    "data": null
  }
  ```

## 2. Tests des Produits (Products)

### 2.1 Liste des produits

- **Méthode** : GET
- **URL** : http://localhost:8000/api/products
- **Réponse attendue** :
  ```json
  {
    "data": [
      {
        "id": 1,
        "reference": "PROD001",
        "name": "Produit 1",
        "description": "Description du produit 1",
        "price": "100.00",
        "quantity": 50,
        "min_stock": 10,
        "category": {
          "id": 1,
          "name": "Catégorie 1"
        }
      },
      // ... autres produits
    ],
    "meta": {
      "resource_type": "Products",
      "total_count": 10,
      "low_stock_count": 2
    },
    "links": {
      "first": "http://localhost:8000/api/products?page=1",
      "last": "http://localhost:8000/api/products?page=1",
      "prev": null,
      "next": null
    },
    "success": true,
    "message": "Liste des produits récupérée avec succès"
  }
  ```

### 2.2 Détails d'un produit

- **Méthode** : GET
- **URL** : http://localhost:8000/api/products/1
- **Réponse attendue** :
  ```json
  {
    "success": true,
    "message": "Produit récupéré avec succès",
    "data": {
      "id": 1,
      "reference": "PROD001",
      "name": "Produit 1",
      "description": "Description du produit 1",
      "price": "100.00",
      "quantity": 50,
      "min_stock": 10,
      "category": {
        "id": 1,
        "name": "Catégorie 1"
      }
    }
  }
  ```

## 3. Tests des Catégories (Categories)

### 3.1 Liste des catégories

- **Méthode** : GET
- **URL** : http://localhost:8000/api/categories
- **Réponse attendue** :
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Catégorie 1",
        "description": "Description de la catégorie 1"
      },
      // ... autres catégories
    ],
    "meta": {
      "resource_type": "Categories",
      "total_count": 5
    },
    "links": {
      "first": "http://localhost:8000/api/categories?page=1",
      "last": "http://localhost:8000/api/categories?page=1",
      "prev": null,
      "next": null
    },
    "success": true,
    "message": "Liste des catégories récupérée avec succès"
  }
  ```

## 4. Tests des Fournisseurs (Suppliers)

### 4.1 Liste des fournisseurs

- **Méthode** : GET
- **URL** : http://localhost:8000/api/suppliers
- **Réponse attendue** :
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Fournisseur 1",
        "contact": "Contact 1",
        "email": "fournisseur1@example.com",
        "phone": "0123456789",
        "address": "Adresse du fournisseur 1"
      },
      // ... autres fournisseurs
    ],
    "meta": {
      "resource_type": "Suppliers",
      "total_count": 5
    },
    "links": {
      "first": "http://localhost:8000/api/suppliers?page=1",
      "last": "http://localhost:8000/api/suppliers?page=1",
      "prev": null,
      "next": null
    },
    "success": true,
    "message": "Liste des fournisseurs récupérée avec succès"
  }
  ```

## 5. Tests des Bons d'entrée (Entry Forms)

### 5.1 Liste des bons d'entrée

- **Méthode** : GET
- **URL** : http://localhost:8000/api/entry-forms
- **Réponse attendue** :
  ```json
  {
    "data": [
      {
        "id": 1,        "reference": "BL001",
        "date": "2023-01-15",
        "status": "validated",
        "comments": "Bon de livraison 1",
        "total": "1000.00",
        "supplier": {
          "id": 1,
          "name": "Fournisseur 1"
        },
        "items": [
          {
            "id": 1,
            "entry_form_id": 1,
            "product": {
              "id": 1,
              "name": "Produit 1",
              "reference": "PROD001"
            },
            "quantity": 10,
            "unit_price": "100.00",
            "total_price": "1000.00"
          }
        ]
      },
      // ... autres bons d'entrée
    ],
    "meta": {
      "resource_type": "EntryForms",
      "total_count": 5,
      "pending_count": 1,
      "validated_count": 3,
      "canceled_count": 1
    },
    "links": {
      "first": "http://localhost:8000/api/entry-forms?page=1",
      "last": "http://localhost:8000/api/entry-forms?page=1",
      "prev": null,
      "next": null
    },
    "success": true,
    "message": "Liste des bons d'entrée récupérée avec succès"
  }
  ```

## 6. Tests des Bons de sortie (Exit Forms)

### 6.1 Liste des bons de sortie

- **Méthode** : GET
- **URL** : http://localhost:8000/api/exit-forms
- **Réponse attendue** :
  ```json
  {
    "data": [
      {
        "id": 1,        "reference": "BS001",
        "date": "2023-01-20",
        "status": "validated",
        "comments": "Bon de sortie 1",
        "destination": "Service technique",
        "reason": "Remplacement d'équipement",
        "items": [
          {
            "id": 1,
            "exit_form_id": 1,
            "product": {
              "id": 1,
              "name": "Produit 1",
              "reference": "PROD001"
            },
            "quantity": 5
          }
        ]
      },
      // ... autres bons de sortie
    ],
    "meta": {
      "resource_type": "ExitForms",
      "total_count": 5,
      "pending_count": 1,
      "validated_count": 3,
      "canceled_count": 1
    },
    "links": {
      "first": "http://localhost:8000/api/exit-forms?page=1",
      "last": "http://localhost:8000/api/exit-forms?page=1",
      "prev": null,
      "next": null
    },
    "success": true,
    "message": "Liste des bons de sortie récupérée avec succès"
  }
  ```

## 7. Tests des Mouvements de stock (Stock Movements)

### 7.1 Liste des mouvements de stock

- **Méthode** : GET
- **URL** : http://localhost:8000/api/stock-movements
- **Réponse attendue** :
  ```json
  {
    "data": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Produit 1",
        "type": "entry",
        "quantity": 10,
        "date": "2023-01-15",
        "reference": "BL001",
        "comments": "Mouvement d'entrée"
      },
      // ... autres mouvements de stock
    ],
    "meta": {
      "resource_type": "StockMovements",
      "total_count": 10,
      "entries_count": 5,
      "exits_count": 5
    },
    "links": {
      "first": "http://localhost:8000/api/stock-movements?page=1",
      "last": "http://localhost:8000/api/stock-movements?page=1",
      "prev": null,
      "next": null
    },
    "success": true,
    "message": "Liste des mouvements de stock récupérée avec succès"
  }
  ```

## 8. Tests de gestion des erreurs

### 8.1 Ressource inexistante

- **Méthode** : GET
- **URL** : http://localhost:8000/api/products/999
- **Réponse attendue** :
  ```json
  {
    "success": false,
    "message": "La ressource demandée n'existe pas",
    "errors": []
  }
  ```

### 8.2 Validation incorrecte

- **Méthode** : POST
- **URL** : http://localhost:8000/api/products
- **Corps** :
  ```json
  {
    "name": "Nouveau produit"
    // Manque le champ 'reference' qui est obligatoire
  }
  ```
- **Réponse attendue** :
  ```json
  {
    "success": false,
    "message": "Les données fournies sont invalides",
    "errors": {
      "reference": [
        "Le champ reference est obligatoire."
      ]
    }
  }
  ```

### 8.3 Accès non authentifié

- **Méthode** : GET
- **URL** : http://localhost:8000/api/products
- **Sans token d'authentification**
- **Réponse attendue** :
  ```json
  {
    "success": false,
    "message": "Authentification requise",
    "errors": []
  }
  ```

## Conclusion

Une fois tous ces tests effectués avec succès, la Phase 8 (Standardisation des réponses API) peut être considérée comme validée. Les réponses sont désormais cohérentes à travers toute l'API, avec une structure standardisée qui facilite l'intégration avec le frontend.

# Documentation des API Resources pour GESTISTOCK

## Structure générale des réponses API

Toutes les réponses API de GESTISTOCK suivent une structure standardisée :

### Réponse de succès

```json
{
  "success": true,
  "message": "Message de succès",
  "data": {
    // Les données renvoyées
  }
}
```

### Réponse d'erreur

```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": {
    // Détails des erreurs
  }
}
```

## Ressources API disponibles

### ProductResource

Représente un produit individuel avec ses attributs.

```json
{
  "id": 1,
  "reference": "PROD001",
  "name": "Nom du produit",
  "description": "Description du produit",
  "price": 19.99,
  "quantity": 50,
  "min_stock": 10,
  "low_stock": false,
  "category": {
    "id": 1,
    "name": "Nom de la catégorie",
    "description": "Description de la catégorie"
  },
  "created_at": "2023-05-20 14:30:00",
  "updated_at": "2023-05-20 14:30:00"
}
```

### CategoryResource

Représente une catégorie de produits.

```json
{
  "id": 1,
  "name": "Nom de la catégorie",
  "description": "Description de la catégorie",
  "products_count": 15,
  "created_at": "2023-05-20 14:30:00",
  "updated_at": "2023-05-20 14:30:00"
}
```

### SupplierResource

Représente un fournisseur.

```json
{
  "id": 1,
  "name": "Nom du fournisseur",
  "contact_person": "John Doe",
  "email": "contact@fournisseur.com",
  "phone": "+33123456789",
  "address": "123 rue du Commerce, 75000 Paris",
  "entries_count": 5,
  "created_at": "2023-05-20 14:30:00",
  "updated_at": "2023-05-20 14:30:00"
}
```

### StockMovementResource

Représente un mouvement de stock.

```json
{
  "id": 1,
  "type": "entry",
  "quantity": 10,
  "date": "2023-05-20 14:30:00",
  "reason": "Réapprovisionnement",
  "product": {
    "id": 1,
    "reference": "PROD001",
    "name": "Nom du produit",
    "description": "Description du produit",
    "price": 19.99,
    "quantity": 50,
    "min_stock": 10
  },
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin"
  },
  "created_at": "2023-05-20 14:30:00",
  "updated_at": "2023-05-20 14:30:00"
}
```

### EntryFormResource

Représente un bon d'entrée.

```json
{
  "id": 1,
  "reference": "ENT-2023-001",
  "date": "2023-05-20",
  "status": "validated",
  "notes": "Livraison complète",
  "total_amount": 199.90,
  "supplier": {
    "id": 1,
    "name": "Nom du fournisseur",
    "contact_person": "John Doe",
    "email": "contact@fournisseur.com",
    "phone": "+33123456789",
    "address": "123 rue du Commerce, 75000 Paris"
  },
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin"
  },
  "items": [
    {
      "id": 1,
      "entry_form_id": 1,
      "product": {
        "id": 1,
        "reference": "PROD001",
        "name": "Nom du produit",
        "description": "Description du produit",
        "price": 19.99
      },
      "quantity": 10,
      "unit_price": 19.99,
      "total_price": 199.90
    }
  ],
  "items_count": 1,
  "created_at": "2023-05-20 14:30:00",
  "updated_at": "2023-05-20 14:30:00"
}
```

### ExitFormResource

Représente un bon de sortie.

```json
{
  "id": 1,
  "reference": "EXT-2023-001",
  "date": "2023-05-20",
  "status": "validated",
  "notes": "Sortie pour le département Marketing",
  "destination": "Département Marketing",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin"
  },
  "items": [
    {
      "id": 1,
      "exit_form_id": 1,
      "product": {
        "id": 1,
        "reference": "PROD001",
        "name": "Nom du produit",
        "description": "Description du produit",
        "price": 19.99
      },
      "quantity": 5
    }
  ],
  "items_count": 1,
  "created_at": "2023-05-20 14:30:00",
  "updated_at": "2023-05-20 14:30:00"
}
```

## Collections

Les collections ajoutent des métadonnées supplémentaires aux listes d'entités.

### ProductCollection

```json
{
  "data": [
    // Tableau de ProductResource
  ],
  "meta": {
    "resource_type": "Products",
    "total_count": 25,
    "low_stock_count": 3
  },
  "links": {
    "first": "http://api.gestistock.com/api/products?page=1",
    "last": "http://api.gestistock.com/api/products?page=3",
    "prev": null,
    "next": "http://api.gestistock.com/api/products?page=2"
  },
  "success": true,
  "message": "Liste des produits récupérée avec succès"
}
```

### StockMovementCollection

```json
{
  "data": [
    // Tableau de StockMovementResource
  ],
  "meta": {
    "resource_type": "StockMovements",
    "total_count": 50,
    "entries_count": 30,
    "exits_count": 20
  },
  "links": {
    "first": "http://api.gestistock.com/api/stock-movements?page=1",
    "last": "http://api.gestistock.com/api/stock-movements?page=5",
    "prev": null,
    "next": "http://api.gestistock.com/api/stock-movements?page=2"
  },
  "success": true,
  "message": "Liste des mouvements de stock récupérée avec succès"
}
```

## Réponses d'erreur standardisées

### Erreur 404 - Ressource non trouvée

```json
{
  "success": false,
  "message": "La ressource demandée n'existe pas",
  "errors": []
}
```

### Erreur 422 - Validation échouée

```json
{
  "success": false,
  "message": "Les données fournies sont invalides",
  "errors": {
    "name": [
      "Le nom est obligatoire"
    ],
    "price": [
      "Le prix doit être un nombre supérieur à zéro"
    ]
  }
}
```

### Erreur 403 - Non autorisé

```json
{
  "success": false,
  "message": "Vous n'êtes pas autorisé à effectuer cette action",
  "errors": []
}
```

### Erreur 401 - Non authentifié

```json
{
  "success": false,
  "message": "Authentification requise",
  "errors": []
}
```

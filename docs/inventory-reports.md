# Rapports d'Inventaire - Documentation API

Ce document décrit les endpoints API disponibles pour générer différents types de rapports d'inventaire dans l'application GESTISTOCK.

## Authentication et Autorisation

Tous les endpoints des rapports nécessitent une authentification JWT et sont accessibles aux utilisateurs ayant le rôle `admin` ou `magasinier`.

## Formats de Réponse

Toutes les réponses suivent le format standard:

```json
{
  "success": true,
  "message": "Message de succès",
  "data": { /* Données de réponse */ }
}
```

## Endpoints de Rapports

### 1. Rapport d'Inventaire Actuel

**Endpoint:** `GET /api/reports/inventory`

**Description:** Retourne l'état actuel de l'inventaire avec les détails des produits, leur statut (normal, faible, rupture) et les regroupements par catégorie.

**Paramètres:**

| Paramètre    | Type     | Obligatoire | Description                                   |
|--------------|----------|-------------|-----------------------------------------------|
| category_id  | integer  | Non         | Filtrer par catégorie                         |
| status       | string   | Non         | Filtrer par statut: "low", "normal", "out"    |
| search       | string   | Non         | Recherche textuelle (nom, référence, desc.)   |
| per_page     | integer  | Non         | Nombre d'éléments par page (défaut: 15)       |
| page         | integer  | Non         | Numéro de page                                |

**Exemple de Réponse:**

```json
{
  "success": true,
  "message": "Rapport d'inventaire généré avec succès",
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Produit A",
        "reference": "REF001",
        "price": 100,
        "quantity": 25,
        "min_stock": 10,
        "category": {
          "id": 1,
          "name": "Catégorie A"
        },
        "stock_status": "normal",
        "total_value": 2500
      }
    ],
    "pagination": {
      "total": 50,
      "per_page": 15,
      "current_page": 1,
      "last_page": 4
    },
    "summary": {
      "total_products": 50,
      "out_of_stock": 5,
      "low_stock": 8,
      "total_value": 45000
    },
    "categories": [
      {
        "id": 1,
        "name": "Catégorie A",
        "product_count": 20,
        "total_value": 25000
      }
    ]
  }
}
```

### 2. Rapport des Mouvements de Stock

**Endpoint:** `GET /api/reports/movements`

**Description:** Retourne l'historique des mouvements de stock avec possibilité de filtrage par date, produit, catégorie et type de mouvement.

**Paramètres:**

| Paramètre    | Type     | Obligatoire | Description                                   |
|--------------|----------|-------------|-----------------------------------------------|
| start_date   | date     | Non         | Date de début (format: YYYY-MM-DD)            |
| end_date     | date     | Non         | Date de fin (format: YYYY-MM-DD)              |
| product_id   | integer  | Non         | Filtrer par produit                           |
| category_id  | integer  | Non         | Filtrer par catégorie                         |
| type         | string   | Non         | Type de mouvement: "entry", "exit"            |
| user_id      | integer  | Non         | Filtrer par utilisateur                       |
| per_page     | integer  | Non         | Nombre d'éléments par page (défaut: 15)       |
| page         | integer  | Non         | Numéro de page                                |

**Exemple de Réponse:**

```json
{
  "success": true,
  "message": "Rapport des mouvements de stock généré avec succès",
  "data": {
    "movements": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Produit A",
          "reference": "REF001"
        },
        "quantity": 10,
        "type": "entry",
        "date": "2023-05-15 10:30:00",
        "user": {
          "id": 1,
          "name": "John Doe"
        }
      }
    ],
    "pagination": {
      "total": 100,
      "per_page": 15,
      "current_page": 1,
      "last_page": 7
    },
    "summary": {
      "total_movements": 100,
      "entries_count": 65,
      "exits_count": 35
    },
    "trends": [
      {
        "period": "2023-05-01",
        "entries": 30,
        "exits": 15,
        "net": 15
      }
    ]
  }
}
```

### 3. Rapport de Valorisation du Stock

**Endpoint:** `GET /api/reports/valuation`

**Description:** Retourne une évaluation financière du stock actuel avec la possibilité de comparer avec une date antérieure.

**Paramètres:**

| Paramètre    | Type     | Obligatoire | Description                                   |
|--------------|----------|-------------|-----------------------------------------------|
| category_id  | integer  | Non         | Filtrer par catégorie                         |
| date         | date     | Non         | Date de valorisation (défaut: aujourd'hui)    |
| compare_date | date     | Non         | Date de comparaison                           |

**Exemple de Réponse:**

```json
{
  "success": true,
  "message": "Rapport de valorisation du stock généré avec succès",
  "data": {
    "current_date": "2023-05-20",
    "total_value": 45000,
    "product_count": 50,
    "by_category": [
      {
        "category": "Catégorie A",
        "product_count": 20,
        "total_value": 25000
      }
    ],
    "products": [...],
    "comparison": {
      "compare_date": "2023-04-20",
      "value_change": 5000,
      "percentage_change": 12.5
    }
  }
}
```

### 4. Rapport de Rotation des Produits

**Endpoint:** `GET /api/reports/turnover`

**Description:** Retourne des statistiques sur la rotation du stock pour analyser la performance des produits.

**Paramètres:**

| Paramètre    | Type     | Obligatoire | Description                                          |
|--------------|----------|-------------|------------------------------------------------------|
| start_date   | date     | Non         | Date de début (défaut: 3 mois avant aujourd'hui)     |
| end_date     | date     | Non         | Date de fin (défaut: aujourd'hui)                    |
| category_id  | integer  | Non         | Filtrer par catégorie                                |
| per_page     | integer  | Non         | Nombre d'éléments par page (défaut: 15)              |
| page         | integer  | Non         | Numéro de page                                       |

**Exemple de Réponse:**

```json
{
  "success": true,
  "message": "Rapport de rotation des produits généré avec succès",
  "data": {
    "products": [
      {
        "id": 1,
        "reference": "REF001",
        "name": "Produit A",
        "category": "Catégorie A",
        "current_quantity": 25,
        "entries": 50,
        "exits": 30,
        "turnover_rate": 4.5,
        "coverage_days": 81,
        "turnover_classification": "medium"
      }
    ],
    "pagination": {
      "total": 50,
      "per_page": 15,
      "current_page": 1,
      "last_page": 4
    },
    "summary": {
      "period_days": 90,
      "average_turnover_rate": 3.2,
      "high_turnover_count": 10,
      "medium_turnover_count": 25,
      "low_turnover_count": 15
    },
    "range": {
      "start_date": "2023-02-20",
      "end_date": "2023-05-20"
    }
  }
}
```

## Mise en Cache

Tous les rapports utilisent un système de mise en cache pour optimiser les performances:
- Durée de mise en cache par défaut: 60 minutes
- La clé du cache est générée en fonction des paramètres de la requête
- Pour obtenir des données fraîches, il suffit de réexécuter la même requête

## Notes d'Implémentation

- Le rapport de rotation utilise la formule: Sorties / Stock moyen
- Le taux de rotation est annualisé pour permettre la comparaison entre différentes périodes
- La classification des taux de rotation suit les règles:
  - Élevé: ≥ 6 rotations par an
  - Moyen: 2-5.99 rotations par an
  - Faible: < 2 rotations par an

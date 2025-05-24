# Documentation des Endpoints du Tableau de Bord

## Introduction

Cette documentation détaille les endpoints API disponibles pour le tableau de bord analytique de GESTISTOCK. Ces API fournissent des analyses et des indicateurs essentiels pour la prise de décision en gestion de stock.

## Authentification

Tous les endpoints du tableau de bord nécessitent une authentification via JWT et sont accessibles aux utilisateurs avec les rôles `admin` et `magasinier`.

## Base URL

```
/api/dashboard/
```

## Format de réponse standard

Toutes les API renvoient des réponses dans un format standardisé:

```json
{
  "success": true,
  "message": "Message décrivant le résultat",
  "data": {
    // Données structurées spécifiques à chaque endpoint
  }
}
```

En cas d'erreur:

```json
{
  "success": false,
  "message": "Description de l'erreur",
  "errors": {
    // Détails des erreurs (si disponibles)
  }
}
```

## Endpoints disponibles

### 1. Vue d'ensemble des KPIs principaux

Fournit une vue synthétique des principaux indicateurs de performance.

```
GET /api/dashboard/summary
```

#### Paramètres

Aucun paramètre requis.

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Aperçu du tableau de bord récupéré avec succès",
  "data": {
    "totalProducts": 125,
    "lowStockAlerts": 18,
    "totalValue": 24350.50,
    "movementsToday": 15,
    "entriesValue": 15420.75,
    "exitsValue": 12780.25
  }
}
```

### 2. Mouvements Récents

Récupère les 5 derniers mouvements de stock (entrées et sorties).

```
GET /api/dashboard/recent-movements
```

#### Paramètres

Aucun paramètre requis.

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Mouvements récents récupérés avec succès",
  "data": [
    {
      "id": 156,
      "productName": "Écran 24 pouces",
      "type": "entry",
      "quantity": 10,
      "date": "2023-05-23T14:32:45+00:00"
    },
    {
      "id": 155,
      "productName": "Clavier sans fil",
      "type": "exit",
      "quantity": 5,
      "date": "2023-05-23T10:15:22+00:00"
    },
    {
      "id": 154,
      "productName": "Papier A4",
      "type": "exit",
      "quantity": 25,
      "date": "2023-05-22T16:45:10+00:00"
    },
    {
      "id": 153,
      "productName": "Souris optique",
      "type": "entry",
      "quantity": 15,
      "date": "2023-05-22T09:30:18+00:00"
    },
    {
      "id": 152,
      "productName": "Bureau standard",
      "type": "exit",
      "quantity": 2,
      "date": "2023-05-21T11:20:05+00:00"
    }
  ]
}
```

### 3. Répartition par catégorie

Fournit des données sur la répartition des produits par catégorie.

```
GET /api/dashboard/category-analysis
```

#### Paramètres

Aucun paramètre requis.

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Tendances des niveaux de stock",
  "data": {
    #### Exemple de réponse

```json
{
  "success": true,
  "message": "Analyse par catégorie récupérée avec succès",
  "data": [
    {
      "name": "Électronique",
      "value": 42,
      "fill": "#8b5cf6"
    },
    {
      "name": "Mobilier",
      "value": 15,
      "fill": "#ec4899"
    },
    {
      "name": "Fournitures",
      "value": 68,
      "fill": "#3b82f6"
    }
  ]
}
```

### 4. Graphique des mouvements de stock

Fournit des données sur les mouvements de stock (entrées et sorties) des 7 derniers jours.

```
GET /api/dashboard/stock-movement-chart
```

#### Paramètres

Aucun paramètre requis.

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Analyse par catégorie",
  "data": {
    "categories": [
      {
        "id": 1,
        "name": "Catégorie A",
        "product_count": 25,
        "total_quantity": 350,
        "total_value": 8750.50,
        "movements_last_30_days": {
          "in": 120,
          "out": 85
        },
        "rotation_rate": 0.24
      },
      // ...autres catégories
    ],
    "period": {
      "start_date": "2023-04-23",
      "end_date": "2023-05-23"
    }
  }
}
```

### 4. Performance des produits

Analyse la performance des produits selon différents critères (rotation, valeur, etc.).

```
GET /api/dashboard/product-performance
```

#### Paramètres

| Paramètre   | Type    | Description                                               | Requis | Défaut    |
|-------------|---------|-----------------------------------------------------------|--------|-----------|
| period      | string  | Période d'analyse ('month', 'quarter', 'year')            | Non    | 'month'   |
| category_id | integer | ID de la catégorie à analyser                             | Non    | null      |
| sort_by     | string  | Critère de tri ('rotation', 'value', 'turnover', 'frequency') | Non    | 'rotation' |
| limit       | integer | Nombre maximum de résultats à retourner                   | Non    | 20        |

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Analyse des performances des produits",
  "data": {
    "period": {
      "type": "month",
      "start_date": "2023-04-23",
      "end_date": "2023-05-23"
    },
    "sort_by": "rotation",
    "products": [
      {
        "id": 16,
        "name": "Produit XYZ",
        "reference": "REF-456",
        "category": {
          "id": 3,
          "name": "Catégorie C"
        },
        "current_stock": {
          "quantity": 45,
          "value": 675.00
        },
        "performance": {
          "quantity_out": 120,
          "rotation_rate": 0.73,
          "turnover": 1800.00,
          "frequency": 18
        }
      },
      // ...autres produits
    ]
  }
}
```

### 5. Indicateurs d'activité

Fournit des métriques détaillées sur l'activité du stock (mouvements, fréquence).

```
GET /api/dashboard/activity-metrics
```

#### Paramètres

| Paramètre | Type   | Description                                            | Requis | Défaut  |
|-----------|--------|--------------------------------------------------------|--------|---------|
| period    | string | Période d'analyse ('week', 'month', 'quarter', 'year') | Non    | 'month' |
| interval  | string | Intervalle de regroupement ('day', 'week', 'month')    | Non    | 'day'   |

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Métriques d'activité",
  "data": {
    "period": {
      "type": "month",
      "interval": "day",
      "start_date": "2023-04-23",
      "end_date": "2023-05-23"
    },
    "summary": {
      "total_movements": 850,
      "total_in_quantity": 1250,
      "total_out_quantity": 980,
      "total_entry_forms": 35,
      "total_exit_forms": 48,
      "average_daily_movements": 28.33
    },
    "activity_by_period": [
      {
        "period": "2023-04-24",
        "date": "2023-04-24",
        "stock_movements": {
          "total": 42,
          "in": 18,
          "out": 24,
          "quantity_in": 65,
          "quantity_out": 48,
          "products_affected": 15
        },
        "forms": {
          "entry": {
            "total": 2,
            "validated": 2,
            "pending": 0,
            "value": 1250.75
          },
          "exit": {
            "total": 3,
            "validated": 2,
            "pending": 1
          }
        }
      },
      // ...autres périodes
    ]
  }
}
```

### 6. Alertes intelligentes

Fournit des alertes sur les ruptures de stock prévues et les surstocks potentiels.

```
GET /api/dashboard/alerts
```

#### Paramètres

Aucun paramètre requis.

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Alertes du tableau de bord",
  "data": {
    "out_of_stock": [
      {
        "id": 34,
        "name": "Produit DEF",
        "reference": "REF-789",
        "category": {
          "id": 2,
          "name": "Catégorie B"
        },
        "severity": "high"
      },
      // ...autres produits en rupture
    ],
    "low_stock": [
      {
        "id": 12,
        "name": "Produit GHI",
        "reference": "REF-101",
        "category": {
          "id": 1,
          "name": "Catégorie A"
        },
        "current_quantity": 5,
        "min_stock": 15,
        "percent_of_min": 33.33,
        "severity": "medium"
      },
      // ...autres produits en stock faible
    ],
    "predicted_stockouts": [
      {
        "id": 45,
        "name": "Produit JKL",
        "reference": "REF-112",
        "category": {
          "id": 3,
          "name": "Catégorie C"
        },
        "current_quantity": 25,
        "daily_consumption": 2.5,
        "estimated_days_remaining": 10,
        "estimated_stockout_date": "2023-06-02",
        "severity": "medium"
      },
      // ...autres produits à risque de rupture
    ],
    "potential_overstocks": [
      {
        "id": 67,
        "name": "Produit MNO",
        "reference": "REF-234",
        "category": {
          "id": 4,
          "name": "Catégorie D"
        },
        "quantity": 120,
        "days_without_movement": 30,
        "value": 3600.00,
        "severity": "medium"
      },
      // ...autres produits potentiellement surstockés
    ]
  }
}
```

## Mise en cache

Les réponses des endpoints de tableau de bord sont mises en cache pour optimiser les performances:
- Les alertes sont mises en cache pendant 5 minutes
- Les autres données sont mises en cache pendant 30 minutes

## Support

Pour tout problème ou question concernant ces endpoints, veuillez contacter l'équipe technique.

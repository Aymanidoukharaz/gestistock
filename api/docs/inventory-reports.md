# Documentation des Rapports d'Inventaire - GESTISTOCK

## Introduction

Les rapports d'inventaire dans GESTISTOCK permettent d'obtenir des données précises et pertinentes sur l'état du stock, les mouvements, la valorisation financière et la rotation des produits. Cette documentation décrit les endpoints disponibles pour générer ces rapports.

## Endpoints disponibles

### 1. Rapport d'Inventaire Actuel

**Endpoint:** `GET /api/reports/inventory`

Ce rapport fournit une vue complète de l'état actuel de l'inventaire, avec des détails sur chaque produit, son statut de stock et des regroupements par catégorie.

#### Paramètres de requête

| Paramètre    | Type    | Description                                                  |
| ------------ | ------- | ------------------------------------------------------------ |
| category_id  | integer | Filtre par identifiant de catégorie (optionnel)              |
| status       | string  | Filtre par statut du stock: 'low', 'normal' ou 'out' (optionnel) |
| search       | string  | Recherche textuelle sur le nom, la référence ou la description (optionnel) |
| per_page     | integer | Nombre d'éléments par page, par défaut 15 (optionnel)        |
| page         | integer | Numéro de page pour la pagination, par défaut 1 (optionnel)  |

#### Exemple de requête

```bash
curl -X GET "http://localhost:8000/api/reports/inventory?category_id=1&status=low&per_page=10" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Rapport d'inventaire généré avec succès",
  "data": {
    "products": [
      {
        "id": 1,
        "reference": "ELEC001",
        "name": "Écran LCD 24\"",
        "description": "Écran haute résolution 1080p",
        "price": 149.99,
        "quantity": 5,
        "min_stock": 10,
        "low_stock": true,
        "category": {
          "id": 1,
          "name": "Électronique"
        },
        "created_at": "2023-05-20 14:30:00",
        "updated_at": "2023-05-20 14:30:00"
      }
    ],
    "pagination": {
      "total": 5,
      "per_page": 10,
      "current_page": 1,
      "last_page": 1
    },
    "summary": {
      "total_products": 20,
      "out_of_stock": 2,
      "low_stock": 5,
      "total_value": 15450.75
    },
    "categories": [
      {
        "id": 1,
        "name": "Électronique",
        "product_count": 8,
        "total_value": 7850.50
      },
      {
        "id": 2,
        "name": "Fournitures de bureau",
        "product_count": 12,
        "total_value": 7600.25
      }
    ]
  }
}
```

### 2. Rapport des Mouvements de Stock

**Endpoint:** `GET /api/reports/movements`

Ce rapport fournit l'historique des entrées et sorties de stock avec des filtres avancés et des analyses de tendances.

#### Paramètres de requête

| Paramètre    | Type    | Description                                                  |
| ------------ | ------- | ------------------------------------------------------------ |
| start_date   | date    | Date de début de la période (format YYYY-MM-DD) (optionnel)  |
| end_date     | date    | Date de fin de la période (format YYYY-MM-DD) (optionnel)    |
| product_id   | integer | Filtre par identifiant de produit (optionnel)                |
| category_id  | integer | Filtre par identifiant de catégorie (optionnel)              |
| type         | string  | Type de mouvement: 'entry' ou 'exit' (optionnel)             |
| user_id      | integer | Filtre par identifiant d'utilisateur (optionnel)             |
| per_page     | integer | Nombre d'éléments par page, par défaut 15 (optionnel)        |
| page         | integer | Numéro de page pour la pagination, par défaut 1 (optionnel)  |

#### Exemple de requête

```bash
curl -X GET "http://localhost:8000/api/reports/movements?start_date=2023-01-01&end_date=2023-12-31&type=exit" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Rapport des mouvements de stock généré avec succès",
  "data": {
    "movements": [
      {
        "id": 25,
        "type": "exit",
        "quantity": 3,
        "date": "2023-05-15 09:30:00",
        "reason": "Livraison au service comptabilité",
        "product": {
          "id": 5,
          "reference": "BUR001",
          "name": "Papier A4",
          "category": {
            "id": 2,
            "name": "Fournitures de bureau"
          }
        },
        "user": {
          "id": 2,
          "name": "Jean Martin",
          "role": "magasinier"
        },
        "created_at": "2023-05-15 09:30:00",
        "updated_at": "2023-05-15 09:30:00"
      }
    ],
    "pagination": {
      "total": 48,
      "per_page": 15,
      "current_page": 1,
      "last_page": 4
    },
    "summary": {
      "total_movements": 48,
      "entries_count": 0,
      "exits_count": 48
    },
    "trends": [
      {
        "period": "2023-01",
        "entries": 0,
        "exits": 12,
        "net": -12
      },
      {
        "period": "2023-02",
        "entries": 0,
        "exits": 8,
        "net": -8
      }
    ]
  }
}
```

### 3. Rapport de Valorisation du Stock

**Endpoint:** `GET /api/reports/valuation`

Ce rapport fournit une analyse financière du stock avec la valeur totale et la ventilation par catégorie.

#### Paramètres de requête

| Paramètre     | Type    | Description                                                  |
| ------------- | ------- | ------------------------------------------------------------ |
| category_id   | integer | Filtre par identifiant de catégorie (optionnel)              |
| date          | date    | Date de valorisation (format YYYY-MM-DD), par défaut aujourd'hui (optionnel) |
| compare_date  | date    | Date de comparaison pour l'évolution (format YYYY-MM-DD) (optionnel) |

#### Exemple de requête

```bash
curl -X GET "http://localhost:8000/api/reports/valuation?compare_date=2023-01-01" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Rapport de valorisation du stock généré avec succès",
  "data": {
    "current_date": "2023-05-20",
    "total_value": 15450.75,
    "product_count": 20,
    "by_category": [
      {
        "category": "Électronique",
        "product_count": 8,
        "total_value": 7850.50
      },
      {
        "category": "Fournitures de bureau",
        "product_count": 12,
        "total_value": 7600.25
      }
    ],
    "products": [
      {
        "id": 1,
        "reference": "ELEC001",
        "name": "Écran LCD 24\"",
        "price": 149.99,
        "quantity": 5,
        "total_value": 749.95,
        "category": {
          "id": 1,
          "name": "Électronique"
        }
      }
    ],
    "comparison": {
      "compare_date": "2023-01-01",
      "value_change": 2500.25,
      "percentage_change": 16.18
    }
  }
}
```

### 4. Rapport de Rotation des Produits

**Endpoint:** `GET /api/reports/turnover`

Ce rapport analyse la rotation des stocks et identifie les produits à forte et faible rotation.

#### Paramètres de requête

| Paramètre    | Type    | Description                                                  |
| ------------ | ------- | ------------------------------------------------------------ |
| start_date   | date    | Date de début de la période (format YYYY-MM-DD), par défaut 3 mois avant aujourd'hui (optionnel) |
| end_date     | date    | Date de fin de la période (format YYYY-MM-DD), par défaut aujourd'hui (optionnel) |
| category_id  | integer | Filtre par identifiant de catégorie (optionnel)              |
| per_page     | integer | Nombre d'éléments par page, par défaut 15 (optionnel)        |
| page         | integer | Numéro de page pour la pagination, par défaut 1 (optionnel)  |

#### Exemple de requête

```bash
curl -X GET "http://localhost:8000/api/reports/turnover?start_date=2023-01-01&end_date=2023-12-31" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

#### Exemple de réponse

```json
{
  "success": true,
  "message": "Rapport de rotation des produits généré avec succès",
  "data": {
    "products": [
      {
        "id": 5,
        "reference": "BUR001",
        "name": "Papier A4",
        "category": "Fournitures de bureau",
        "current_quantity": 45,
        "entries": 100,
        "exits": 150,
        "turnover_rate": 7.5,
        "coverage_days": 49,
        "turnover_classification": "high"
      }
    ],
    "pagination": {
      "total": 20,
      "per_page": 15,
      "current_page": 1,
      "last_page": 2
    },
    "summary": {
      "period_days": 365,
      "average_turnover_rate": 4.2,
      "high_turnover_count": 6,
      "medium_turnover_count": 10,
      "low_turnover_count": 4
    },
    "range": {
      "start_date": "2023-01-01",
      "end_date": "2023-12-31"
    }
  }
}
```

## Codes d'état HTTP

| Code | Description                                                  |
| ---- | ------------------------------------------------------------ |
| 200  | Succès - Le rapport a été généré correctement                 |
| 400  | Erreur de requête - Les paramètres fournis sont incorrects    |
| 401  | Non autorisé - Authentification requise                       |
| 403  | Interdit - L'utilisateur n'a pas les permissions nécessaires  |
| 422  | Données invalides - Les paramètres fournis ne respectent pas les règles de validation |
| 500  | Erreur serveur - Une erreur est survenue lors du traitement de la requête |

## Notes d'implémentation

- Les rapports utilisent le cache pour améliorer les performances.
- La période de mise en cache par défaut est de 60 minutes.
- Les filtres permettent d'affiner les résultats selon les besoins spécifiques.
- Les calculs financiers utilisent des types décimaux appropriés pour éviter les erreurs d'arrondi.
- La pagination est implémentée sur tous les rapports pour gérer efficacement les grands volumes de données.

## Exemples d'utilisation avec curl

### Rapport d'inventaire pour une catégorie spécifique

```bash
curl -X GET "http://localhost:8000/api/reports/inventory?category_id=2" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Rapport des mouvements de stock pour un produit spécifique

```bash
curl -X GET "http://localhost:8000/api/reports/movements?product_id=3&start_date=2023-01-01&end_date=2023-12-31" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Rapport de valorisation avec comparaison sur 3 mois

```bash
# Obtenir la date d'il y a 3 mois
COMPARE_DATE=$(date -d "3 months ago" +%Y-%m-%d)

curl -X GET "http://localhost:8000/api/reports/valuation?compare_date=$COMPARE_DATE" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Rapport de rotation des produits à faible rotation

```bash
curl -X GET "http://localhost:8000/api/reports/turnover?start_date=2023-01-01&end_date=2023-12-31" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" | grep -A 20 "\"turnover_classification\":\"low\""
```

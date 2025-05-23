# Documentation de la Gestion des Stocks

Ce document décrit le système de gestion des stocks implémenté dans l'application GESTISTOCK.

## Flux des Opérations de Stock

### 1. Processus des Bons d'Entrée

1. **Création (statut: draft)**
   - Un utilisateur crée un bon d'entrée avec une liste de produits
   - Aucun impact sur le stock à ce stade
   - Le bon reste modifiable

2. **Validation (statut: pending → completed)**
   - Un utilisateur autorisé valide le bon d'entrée
   - Le système vérifie que le bon est en statut "draft"
   - Le système change temporairement le statut en "pending"
   - Pour chaque produit:
     - La quantité en stock est augmentée
     - Le prix du produit est mis à jour si nécessaire
     - Un mouvement de stock de type "entry" est créé
   - Le statut final devient "completed"
   - Le bon n'est plus modifiable

### 2. Processus des Bons de Sortie

1. **Création (statut: draft)**
   - Un utilisateur crée un bon de sortie avec une liste de produits
   - Aucun impact sur le stock à ce stade
   - Le bon reste modifiable

2. **Validation (statut: pending → completed)**
   - Un utilisateur autorisé valide le bon de sortie
   - Le système vérifie que le bon est en statut "draft"
   - Le système vérifie la disponibilité des quantités pour tous les produits
   - Le système change temporairement le statut en "pending"
   - Pour chaque produit:
     - La quantité en stock est diminuée
     - Un mouvement de stock de type "exit" est créé
   - Le statut final devient "completed"
   - Le bon n'est plus modifiable

## Statuts et leurs Significations

- **draft**: Bon nouvellement créé, modifiable, sans impact sur le stock
- **pending**: État transitoire pendant la validation
- **completed**: Bon validé, non modifiable, ayant impacté le stock

## Règles de Gestion

1. **Règles générales**
   - Les utilisateurs authentifiés peuvent créer des bons d'entrée/sortie
   - Seuls les utilisateurs autorisés (admin, magasinier) peuvent valider des bons

2. **Règles de validation**
   - Un bon ne peut être validé que s'il est en statut "draft"
   - Une fois validé (statut "completed"), un bon ne peut plus être modifié

3. **Règles de stock**
   - Une sortie est interdite si la quantité disponible est insuffisante
   - Une notification est générée si un produit passe sous son seuil minimum (min_stock)
   - Chaque mouvement de stock enregistre l'utilisateur qui effectue l'action

## Services Implémentés

1. **StockService**
   - Gère les opérations de base sur le stock
   - Met à jour les quantités et prix des produits
   - Vérifie les niveaux de stock
   - Crée les mouvements de stock

2. **EntryService**
   - Gère la validation des bons d'entrée
   - Utilise StockService pour mettre à jour le stock

3. **ExitService**
   - Gère la validation des bons de sortie
   - Vérifie la disponibilité du stock
   - Utilise StockService pour mettre à jour le stock

## Endpoints API

1. **Bons d'Entrée**
   - `POST /api/entry-forms/{id}/validate`: Valider un bon d'entrée

2. **Bons de Sortie**
   - `POST /api/exit-forms/{id}/validate`: Valider un bon de sortie

## Gestion des Erreurs

Le système gère plusieurs types d'erreurs:
- Tentative de validation d'un bon qui n'est pas en statut "draft"
- Stock insuffisant pour une sortie
- Produit ou bon non trouvé

Chaque erreur est documentée dans les logs du système et retournée à l'utilisateur avec un message explicatif.

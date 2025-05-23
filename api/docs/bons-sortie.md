# Bons de Sortie - GESTISTOCK

## Introduction

Les bons de sortie dans GESTISTOCK permettent de documenter et gérer toutes les sorties de stocks. Ce document décrit les fonctionnalités avancées implémentées pour les bons de sortie, incluant la gestion des statuts, la validation, l'annulation, et l'historique des modifications.

## Modèle de données

Un bon de sortie comprend les informations suivantes :

- **Référence** : Identifiant unique du bon de sortie
- **Date** : Date de création du bon
- **Destination** : Lieu ou service de destination des produits
- **Motif** : Raison de la sortie
- **Notes** : Informations complémentaires
- **Statut** : État actuel du bon (draft, pending, completed, cancelled)
- **Utilisateur** : Personne qui a créé le bon
- **Articles** : Liste des produits sortis avec leurs quantités

## Cycle de vie d'un bon de sortie

1. **Brouillon (draft)** : Le bon est créé mais n'a pas encore d'impact sur le stock.
2. **En attente (pending)** : Le bon est en cours de validation.
3. **Validé (completed)** : Le bon est validé et le stock est mis à jour.
4. **Annulé (cancelled)** : Le bon a été annulé et n'a plus d'impact sur le stock.

## Fonctionnalités avancées

### Détection des doublons

GESTISTOCK détecte automatiquement les doublons potentiels de bons de sortie en se basant sur la référence. Cela permet d'éviter les enregistrements en double et de maintenir l'intégrité des données.

### Validation des bons de sortie

Le processus de validation d'un bon de sortie comprend :
1. Vérification que le bon est en statut "draft"
2. Vérification que le bon contient au moins un article
3. Vérification que la date n'est pas dans le futur
4. Vérification de la disponibilité du stock pour tous les articles
5. Mise à jour du stock (réduction des quantités)
6. Enregistrement des mouvements de stock
7. Changement du statut en "completed"

### Annulation des bons de sortie

L'annulation d'un bon de sortie permet de :
1. Réintégrer les produits dans le stock si le bon était en statut "completed"
2. Enregistrer les mouvements de stock correspondants
3. Marquer le bon comme "cancelled"
4. Consigner la raison de l'annulation

### Historique des modifications

GESTISTOCK enregistre toutes les modifications apportées aux bons de sortie, y compris :
- La personne qui a effectué la modification
- Le champ modifié
- L'ancienne valeur
- La nouvelle valeur
- La date de modification
- La raison de la modification

### Rapports et analyses

GESTISTOCK permet de générer des rapports sur les bons de sortie :
- Rapport par période (entre deux dates)
- Rapport par destination

## API disponible

| Méthode | URL                                  | Description                                   | Rôles autorisés      |
|---------|--------------------------------------|-----------------------------------------------|--------------------- |
| GET     | /api/exit-forms                      | Liste des bons de sortie                      | Tous                 |
| GET     | /api/exit-forms/{id}                 | Détail d'un bon de sortie                     | Tous                 |
| POST    | /api/exit-forms                      | Créer un bon de sortie                        | Admin, Magasinier    |
| PUT     | /api/exit-forms/{id}                 | Modifier un bon de sortie                     | Admin                |
| DELETE  | /api/exit-forms/{id}                 | Supprimer un bon de sortie                    | Admin                |
| POST    | /api/exit-forms/{id}/validate        | Valider un bon de sortie                      | Admin, Magasinier    |
| POST    | /api/exit-forms/{id}/cancel          | Annuler un bon de sortie                      | Admin                |
| GET     | /api/exit-forms/{id}/history         | Historique des modifications                  | Admin, Magasinier    |
| POST    | /api/exit-forms/check-duplicates     | Vérifier les doublons                         | Admin, Magasinier    |
| GET     | /api/reports/exits/by-period         | Rapport des sorties par période               | Admin, Magasinier    |
| GET     | /api/reports/exits/by-destination    | Rapport des sorties par destination           | Admin, Magasinier    |

## Exemples d'utilisation

### Créer un bon de sortie

```json
POST /api/exit-forms
{
  "reference": "BS-2025-001",
  "date": "2025-05-20",
  "destination": "Service Technique",
  "reason": "Maintenance préventive",
  "notes": "Sortie de matériel pour maintenance mensuelle",
  "status": "draft",
  "user_id": 1,
  "items": [
    {
      "product_id": 3,
      "quantity": 5
    },
    {
      "product_id": 7,
      "quantity": 2
    }
  ]
}
```

### Valider un bon de sortie

```json
POST /api/exit-forms/1/validate
{
  "validation_note": "Validation après vérification physique"
}
```

### Annuler un bon de sortie

```json
POST /api/exit-forms/1/cancel
{
  "reason": "Erreur de saisie"
}
```

### Vérifier les doublons

```json
POST /api/exit-forms/check-duplicates
{
  "reference": "BS-2025-001"
}
```

## Erreurs courantes et solutions

| Erreur | Description | Solution |
|--------|-------------|----------|
| "Le bon de sortie doit être en statut 'draft' pour être validé" | Tentative de valider un bon qui n'est pas en brouillon | Vérifier le statut du bon avant validation |
| "Stock insuffisant pour le produit X" | Quantité en stock insuffisante pour la sortie | Vérifier les quantités demandées ou approvisionner le stock |
| "Impossible de valider un bon de sortie sans articles" | Tentative de valider un bon sans articles | Ajouter au moins un article au bon |
| "La date du bon de sortie ne peut pas être dans le futur" | Date invalide | Corriger la date du bon |
| "Ce bon de sortie est déjà annulé" | Tentative d'annuler un bon déjà annulé | Aucune action nécessaire |

## Bonnes pratiques

1. Toujours vérifier les quantités en stock avant de valider un bon de sortie
2. Fournir des raisons claires pour les annulations
3. Vérifier les doublons potentiels avant de créer un nouveau bon
4. Consulter régulièrement les rapports pour analyser les tendances
5. Maintenir à jour la documentation des destinations et motifs de sortie

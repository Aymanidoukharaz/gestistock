# Documentation des Bons d'Entrée

## Présentation
Les bons d'entrée représentent les mouvements d'entrée de produits dans le stock. Ils sont généralement associés à des livraisons de fournisseurs et permettent de tracer l'origine et la quantité des produits ajoutés au stock.

## Statuts des Bons d'Entrée
- **draft** (brouillon) : Le bon est en cours de création et peut être modifié
- **pending** (en attente) : Le bon est en cours de validation
- **completed** (terminé) : Le bon a été validé et les produits ont été ajoutés au stock
- **cancelled** (annulé) : Le bon a été annulé, les produits ne sont pas ou plus ajoutés au stock

## Flux de traitement
1. Création du bon d'entrée (statut: draft)
2. Ajout des articles au bon
3. Validation du bon (statut: pending -> completed)
4. Impact sur le stock (augmentation des quantités)

## Annulation d'un bon d'entrée
Lorsqu'un bon d'entrée est annulé:
- Si le bon est en statut "draft", il peut être simplement marqué comme "cancelled" sans impact sur le stock
- Si le bon est déjà en statut "completed", l'annulation entraînera une correction du stock (réduction des quantités ajoutées précédemment)

## Vérification des doublons
Le système vérifie automatiquement si un bon d'entrée potentiellement en doublon existe déjà, en se basant sur:
- Le fournisseur
- La référence
- La date
- La composition similaire des articles

## Historique des modifications
Chaque modification d'un bon d'entrée est enregistrée dans un historique, permettant de tracer:
- Qui a effectué la modification
- Quand la modification a été effectuée
- Quel champ a été modifié
- Les valeurs avant/après la modification

## Contraintes de validation
Les bons d'entrée sont soumis à plusieurs contraintes de validation:
- La référence doit être unique
- La date ne peut pas être dans le futur
- Les quantités doivent être positives
- Le fournisseur doit exister
- Les produits doivent exister

## Rapports disponibles
Plusieurs rapports sont disponibles pour analyser les entrées:
- Entrées par période
- Entrées par fournisseur
- Entrées par produit
- Analyse des prix d'achat

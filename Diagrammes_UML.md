# Diagrammes UML pour l'Application de Gestion de Stock

## 1. Diagramme de Cas d'Utilisation pour l'Administrateur

```mermaid
graph TD
    A[Administrateur] --> (Gérer les Utilisateurs)
    A --> (Gérer les Produits)
    A --> (Gérer les Catégories)
    A --> (Gérer les Fournisseurs)
    A --> (Gérer les Mouvements de Stock)
    A --> (Gérer les Bons d'Entrée)
    A --> (Gérer les Bons de Sortie)
    A --> (Consulter le Tableau de Bord)

    subgraph Gestion des Utilisateurs
        (Gérer les Utilisateurs) --> (Créer un Utilisateur)
        (Gérer les Utilisateurs) --> (Mettre à jour un Utilisateur)
        (Gérer les Utilisateurs) --> (Activer/Désactiver un Utilisateur)
        (Gérer les Utilisateurs) --> (Supprimer un Utilisateur)
        (Gérer les Utilisateurs) --> (Consulter les Utilisateurs)
    end

    subgraph Gestion des Produits
        (Gérer les Produits) --> (Créer un Produit)
        (Gérer les Produits) --> (Mettre à jour un Produit)
        (Gérer les Produits) --> (Supprimer un Produit)
        (Gérer les Produits) --> (Consulter les Produits)
    end

    subgraph Gestion des Catégories
        (Gérer les Catégories) --> (Créer une Catégorie)
        (Gérer les Catégories) --> (Mettre à jour une Catégorie)
        (Gérer les Catégories) --> (Supprimer une Catégorie)
        (Gérer les Catégories) --> (Consulter les Catégories)
    end

    subgraph Gestion des Fournisseurs
        (Gérer les Fournisseurs) --> (Créer un Fournisseur)
        (Gérer les Fournisseurs) --> (Mettre à jour un Fournisseur)
        (Gérer les Fournisseurs) --> (Supprimer un Fournisseur)
        (Gérer les Fournisseurs) --> (Consulter les Fournisseurs)
    end

    subgraph Gestion des Mouvements de Stock
        (Gérer les Mouvements de Stock) --> (Créer un Mouvement de Stock)
        (Gérer les Mouvements de Stock) --> (Mettre à jour un Mouvement de Stock)
        (Gérer les Mouvements de Stock) --> (Supprimer un Mouvement de Stock)
        (Gérer les Mouvements de Stock) --> (Consulter les Mouvements de Stock)
        (Gérer les Mouvements de Stock) --> (Consulter les Mouvements par Produit)
    end

    subgraph Gestion des Bons d'Entrée
        (Gérer les Bons d'Entrée) --> (Créer un Bon d'Entrée)
        (Gérer les Bons d'Entrée) --> (Mettre à jour un Bon d'Entrée)
        (Gérer les Bons d'Entrée) --> (Supprimer un Bon d'Entrée)
        (Gérer les Bons d'Entrée) --> (Consulter les Bons d'Entrée)
        (Gérer les Bons d'Entrée) --> (Valider un Bon d'Entrée)
        (Gérer les Bons d'Entrée) --> (Annuler un Bon d'Entrée)
        (Gérer les Bons d'Entrée) --> (Vérifier les Doublons de Bons d'Entrée)
        (Gérer les Bons d'Entrée) --> (Consulter l'Historique des Bons d'Entrée)
    end

    subgraph Gestion des Bons de Sortie
        (Gérer les Bons de Sortie) --> (Créer un Bon de Sortie)
        (Gérer les Bons de Sortie) --> (Mettre à jour un Bon de Sortie)
        (Gérer les Bons de Sortie) --> (Supprimer un Bon de Sortie)
        (Gérer les Bons de Sortie) --> (Consulter les Bons de Sortie)
        (Gérer les Bons de Sortie) --> (Valider un Bon de Sortie)
        (Gérer les Bons de Sortie) --> (Annuler un Bon de Sortie)
        (Gérer les Bons de Sortie) --> (Vérifier les Doublons de Bons de Sortie)
        (Gérer les Bons de Sortie) --> (Consulter l'Historique des Bons de Sortie)
    end

    subgraph Tableau de Bord
        (Consulter le Tableau de Bord) --> (Consulter le Résumé du Stock)
        (Consulter le Tableau de Bord) --> (Consulter les Mouvements Récents)
        (Consulter le Tableau de Bord) --> (Analyser le Stock par Catégorie)
        (Consulter le Tableau de Bord) --> (Consulter les Graphiques de Mouvements de Stock)
    end
```

## 2. Diagramme de Cas d'Utilisation pour le Magasinier

```mermaid
graph TD
    M[Magasinier] --> (Consulter les Produits)
    M --> (Gérer les Mouvements de Stock)
    M --> (Gérer les Bons d'Entrée)
    M --> (Gérer les Bons de Sortie)
    M --> (Consulter le Tableau de Bord)

    subgraph Consultation des Produits
        (Consulter les Produits) --> (Consulter les Détails d'un Produit)
        (Consulter les Produits) --> (Consulter les Catégories de Produits)
    end


    subgraph Consultation des Mouvements de Stock
        (Consulter les Mouvements de Stock) --> (Consulter les Détails d'un Mouvement de Stock)
        (Gérer les Mouvements de Stock) --> (Gérer les Mouvements par Produit)
    end

    subgraph Gestion des Bons d'Entrée
        (Gérer les Bons d'Entrée) --> (Créer un Bon d'Entrée)
        (Gérer les Bons d'Entrée) --> (Consulter les Bons d'Entrée)
        (Gérer les Bons d'Entrée) --> (Valider un Bon d'Entrée)
        (Gérer les Bons d'Entrée) --> (Vérifier les Doublons de Bons d'Entrée)
        (Gérer les Bons d'Entrée) --> (Consulter l'Historique des Bons d'Entrée)
    end

    subgraph Gestion des Bons de Sortie
        (Gérer les Bons de Sortie) --> (Créer un Bon de Sortie)
        (Gérer les Bons de Sortie) --> (Consulter les Bons de Sortie)
        (Gérer les Bons de Sortie) --> (Valider un Bon de Sortie)
        (Gérer les Bons de Sortie) --> (Vérifier les Doublons de Bons de Sortie)
        (Gérer les Bons de Sortie) --> (Consulter l'Historique des Bons de Sortie)
    end

    subgraph Tableau de Bord
        (Consulter le Tableau de Bord) --> (Consulter le Résumé du Stock)
        (Consulter le Tableau de Bord) --> (Consulter les Mouvements Récents)
        (Consulter le Tableau de Bord) --> (Analyser le Stock par Catégorie)
        (Consulter le Tableau de Bord) --> (Consulter les Graphiques de Mouvements de Stock)
    end
```

## 3. Diagramme de Classes

```mermaid
classDiagram
    class Category {
        +string id
        +string name
        +string description
        +string created_at
        +string updated_at
        +number products_count
    }

    class Product {
        +string id
        +string reference
        +string name
        +string description
        +number price
        +number quantity
        +number min_stock
    }

    class Supplier {
        +string id
        +string name
        +string email
        +string phone
        +string address
        +string notes
    }

    class User {
        +number id
        +string name
        +string email
        +string role
        +boolean active
        +string password
    }

    class EntryForm {
        +string id
        +string reference
        +string date
        +string supplierId
        +string supplierName
        +string notes
        +string status
        +number total
    }

    class EntryItem {
        +string id
        +string productId
        +string productName
        +number quantity
        +number unitPrice
        +number total
    }

    class ExitForm {
        +string id
        +string reference
        +string date
        +string destination
        +string reason
        +string notes
        +string status
        +number total
    }

    class ExitItem {
        +string id
        +number quantity
    }

    class StockMovement {
        +string id
        +string type
        +number quantity
        +string date
        +string reason
        +string created_at
        +string updated_at
    }

    Category "1" -- "0..*" Product : contains
    Product "1" -- "0..*" EntryItem : includes
    Product "1" -- "0..*" ExitItem : includes
    Product "1" -- "0..*" StockMovement : concerns
    Supplier "1" -- "0..*" EntryForm : provides
    EntryForm "1" -- "1..*" EntryItem : contains
    ExitForm "1" -- "1..*" ExitItem : contains
    User "1" -- "0..*" EntryForm : created by
    User "1" -- "0..*" ExitForm : created by
    User "1" -- "0..*" StockMovement : recorded by
    EntryItem "1" -- "1" Product : references
    ExitItem "1" -- "1" Product : references
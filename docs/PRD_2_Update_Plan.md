# Plan de Mise à Jour du Document de Spécifications Produit (DSP) - Application de Gestion de Stock

## Objectif

Mettre à jour le document [`PRD_2.md`](PRD_2.md) pour refléter avec précision les dernières routes API et les restrictions d'accès basées sur les rôles, en particulier pour la consultation des catégories et des fournisseurs, ainsi que le nouveau point de terminaison de débogage pour les bons d'entrée.

## Étapes

1.  **Analyse de `api/routes/api.php` et `PRD_2.md`:**
    *   Confirmer l'état actuel des points de terminaison API et de leurs rôles associés dans [`api/routes/api.php`](api/routes/api.php).
    *   Comparer ces informations avec la section "6. Points de Terminaison API (Inférés)" dans [`PRD_2.md`](PRD_2.md).
    *   Identifier les mises à jour spécifiques requises :
        *   **Nouveau Point de Terminaison API :** Ajouter `GET /entry-forms/{entryForm}/debug` avec un accès pour les rôles `admin,magasinier`.
        *   **Restriction de Rôle pour les Catégories :** Mettre à jour l'accès pour `GET /categories` et `GET /categories/{category)` de `Admin/Magasinier` à `Admin uniquement`.
        *   **Restriction de Rôle pour les Fournisseurs :** Mettre à jour l'accès pour `GET /suppliers` et `GET /suppliers/{supplier}` de `Admin/Magasinier` à `Admin uniquement`.

2.  **Mise à jour de la section "6. Points de Terminaison API (Inférés)" dans `PRD_2.md`:**
    *   **Pour la section "Catégories" :**
        *   Modifier l'accès pour `GET /categories` et `GET /categories/{category}` à `(Protégé, Admin uniquement)`.
    *   **Pour la section "Fournisseurs" :**
        *   Modifier l'accès pour `GET /suppliers` et `GET /suppliers/{supplier}` à `(Protégé, Admin uniquement)`.
    *   **Pour la section "Bons d'Entrée" :**
        *   Ajouter une nouvelle entrée pour le point de terminaison de débogage :
            *   `GET /entry-forms/{entryForm}/debug` : Obtenir les informations de débogage pour un bon d'entrée spécifique (Protégé, Admin/Magasinier).

3.  **Révision et Affinement :**
    *   Revoir les sections mises à jour dans `PRD_2.md` pour assurer la clarté, la précision et la cohérence avec le fichier `api/routes/api.php` et les dernières clarifications de l'utilisateur.
    *   S'assurer que la langue reste cohérente (français).

## Diagramme Mermaid des Changements de Points de Terminaison API

```mermaid
graph TD
    subgraph Mises à jour des Points de Terminaison API
        A[GET /entry-forms/{entryForm}/debug] --> B{Accès: Admin/Magasinier};
        C[GET /categories] --> D{Accès: Admin uniquement};
        E[GET /categories/{category}] --> F{Accès: Admin uniquement};
        G[GET /suppliers] --> H{Accès: Admin uniquement};
        I[GET /suppliers/{supplier}] --> J{Accès: Admin uniquement};
    end
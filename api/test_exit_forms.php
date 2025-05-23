<?php
/**
 * Ce fichier contient des exemples de requêtes pour tester les fonctionnalités
 * des bons de sortie dans GESTISTOCK via Postman.
 * 
 * Pour utiliser ce fichier:
 * 1. Importez les requêtes dans Postman
 * 2. Remplacez {{base_url}} par l'URL de votre API (par exemple http://localhost/SFE/api/public/api)
 * 3. Remplacez {{jwt_token}} par un token JWT valide obtenu via la route de login
 */

// 1. CRÉER UN BON DE SORTIE
/*
POST {{base_url}}/exit-forms
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reference": "BS-2025-001",
  "date": "2025-05-23",
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
*/

// 2. RÉCUPÉRER UN BON DE SORTIE
/*
GET {{base_url}}/exit-forms/1
Headers:
  Authorization: Bearer {{jwt_token}}
  Accept: application/json
*/

// 3. METTRE À JOUR UN BON DE SORTIE
/*
PUT {{base_url}}/exit-forms/1
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reference": "BS-2025-001",
  "date": "2025-05-23",
  "destination": "Service Informatique",
  "reason": "Maintenance matériel",
  "notes": "Mise à jour des notes",
  "status": "draft",
  "user_id": 1,
  "items": [
    {
      "product_id": 3,
      "quantity": 3
    },
    {
      "product_id": 7,
      "quantity": 4
    }
  ]
}
*/

// 4. SUPPRIMER UN BON DE SORTIE
/*
DELETE {{base_url}}/exit-forms/1
Headers:
  Authorization: Bearer {{jwt_token}}
  Accept: application/json
*/

// 5. VALIDER UN BON DE SORTIE
/*
POST {{base_url}}/exit-forms/1/validate
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "validation_note": "Validation après vérification physique"
}
*/

// 6. VÉRIFIER LES DOUBLONS
/*
POST {{base_url}}/exit-forms/check-duplicates
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reference": "BS-2025-001"
}
*/

// 7. ANNULER UN BON DE SORTIE
/*
POST {{base_url}}/exit-forms/1/cancel
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reason": "Erreur de saisie"
}
*/

// 8. CONSULTER L'HISTORIQUE D'UN BON DE SORTIE
/*
GET {{base_url}}/exit-forms/1/history
Headers:
  Authorization: Bearer {{jwt_token}}
  Accept: application/json
*/

// 9. GÉNÉRER UN RAPPORT PAR PÉRIODE
/*
GET {{base_url}}/reports/exits/by-period?start_date=2025-01-01&end_date=2025-05-30
Headers:
  Authorization: Bearer {{jwt_token}}
  Accept: application/json
*/

// 10. GÉNÉRER UN RAPPORT PAR DESTINATION
/*
GET {{base_url}}/reports/exits/by-destination?start_date=2025-01-01&end_date=2025-05-30
Headers:
  Authorization: Bearer {{jwt_token}}
  Accept: application/json
*/

// 11. LISTE DES BONS DE SORTIE
/*
GET {{base_url}}/exit-forms
Headers:
  Authorization: Bearer {{jwt_token}}
  Accept: application/json
*/

/**
 * TESTS DE SCÉNARIOS D'ERREUR
 */

// 1. VALIDER UN BON DE SORTIE AVEC STOCK INSUFFISANT
/*
POST {{base_url}}/exit-forms
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reference": "BS-2025-ERROR",
  "date": "2025-05-23",
  "destination": "Service Technique",
  "reason": "Test erreur stock",
  "notes": "Ce bon devrait échouer à la validation",
  "status": "draft",
  "user_id": 1,
  "items": [
    {
      "product_id": 3,
      "quantity": 99999
    }
  ]
}
*/

// 2. VALIDER UN BON DE SORTIE SANS ARTICLES
/*
POST {{base_url}}/exit-forms
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reference": "BS-2025-EMPTY",
  "date": "2025-05-23",
  "destination": "Service Technique",
  "reason": "Test erreur sans articles",
  "notes": "Ce bon ne contient pas d'articles",
  "status": "draft",
  "user_id": 1,
  "items": []
}
*/

// 3. VALIDER UN BON AVEC DATE FUTURE
/*
POST {{base_url}}/exit-forms
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reference": "BS-2025-FUTURE",
  "date": "2026-05-23",
  "destination": "Service Technique",
  "reason": "Test erreur date future",
  "notes": "Ce bon a une date dans le futur",
  "status": "draft",
  "user_id": 1,
  "items": [
    {
      "product_id": 3,
      "quantity": 1
    }
  ]
}
*/

// 4. ANNULER UN BON DE SORTIE DÉJÀ ANNULÉ
/*
POST {{base_url}}/exit-forms/1/cancel
Headers:
  Authorization: Bearer {{jwt_token}}
  Content-Type: application/json
  Accept: application/json
Body:
{
  "reason": "Tentative d'annulation d'un bon déjà annulé"
}
*/

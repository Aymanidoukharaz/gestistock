# Correction de l'erreur "Route [login] not defined" et Documentation API

## Modifications apportées

### 1. Modification du Middleware d'Authentification

Nous avons modifié le middleware `Authenticate.php` pour gérer correctement les requêtes d'API :
- Pour les requêtes API, nous ne redirigeons plus vers une route nommée "login"
- Nous lançons simplement une exception `AuthenticationException` qui est gérée par le Handler d'exceptions

### 2. Ajout du Middleware RedirectIfAuthenticated

Un nouveau middleware `RedirectIfAuthenticated.php` a été créé pour :
- Gérer correctement les requêtes API lorsqu'un utilisateur est déjà authentifié
- Renvoyer une réponse JSON au lieu de rediriger pour les requêtes API

### 3. Nommage explicite de la route de connexion

Dans `routes/api.php`, nous avons nommé explicitement la route de connexion :
```php
Route::post('login', [AuthController::class, 'login'])->name('login');
```

### 4. Ajout de la constante HOME dans RouteServiceProvider

Nous avons ajouté la constante `HOME` dans `RouteServiceProvider.php` pour définir la page d'accueil après authentification.

## Structure des réponses API

Toutes les réponses API suivent maintenant une structure standardisée grâce à la classe `ApiResponse` :

### Réponses de succès
```json
{
    "success": true,
    "message": "Message de succès",
    "data": {
        // Données renvoyées
    }
}
```

### Réponses d'erreur
```json
{
    "success": false,
    "message": "Message d'erreur",
    "errors": {
        // Détails des erreurs (facultatif)
    }
}
```

### Codes HTTP utilisés
- **200** : Succès
- **400** : Erreur de requête
- **401** : Non authentifié
- **403** : Non autorisé
- **404** : Ressource non trouvée
- **422** : Erreur de validation

## Test de l'API

Pour tester l'authentification de l'API :

1. **Login** :
   - Méthode : POST
   - URL : http://localhost:8000/api/auth/login
   - Corps (JSON) :
   ```json
   {
       "email": "votre_email@example.com",
       "password": "votre_mot_de_passe"
   }
   ```

2. **Accès à une ressource protégée** :
   - Ajouter l'en-tête d'autorisation : `Authorization: Bearer {token}`

3. **Déconnexion** :
   - Méthode : POST
   - URL : http://localhost:8000/api/auth/logout
   - Avec l'en-tête d'autorisation

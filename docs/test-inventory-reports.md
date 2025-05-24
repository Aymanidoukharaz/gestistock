# Tests des Endpoints de Rapports d'Inventaire

Ces commandes PowerShell peuvent être utilisées pour tester les endpoints des rapports d'inventaire après leur implémentation.

## Prérequis
1. XAMPP ou un serveur PHP local doit être en cours d'exécution
2. L'application Laravel doit être correctement configurée
3. La base de données doit contenir des données de test

## Comment utiliser ce script
1. Ouvrez PowerShell
2. Copiez et collez les commandes individuellement pour tester chaque endpoint
3. Adaptez l'URL de base si nécessaire (par défaut: http://localhost/SFE/api/public)
4. Remplacez `YOUR_TOKEN_HERE` par un token JWT valide obtenu après connexion

## Obtenir un token JWT
```powershell
$loginData = @{
    email = "admin@example.com"
    password = "password"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/auth/login" -Method Post -Body $loginData -ContentType "application/json"
$token = $response.data.token

# Afficher le token pour utilisation dans les commandes suivantes
Write-Host "Bearer $token"
```

## Test du Rapport d'Inventaire
```powershell
# Sans filtres
$headers = @{ Authorization = "Bearer YOUR_TOKEN_HERE" }
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/inventory" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Avec filtres
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/inventory?category_id=1&status=low&per_page=10" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Avec recherche
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/inventory?search=produit" -Method Get -Headers $headers | ConvertTo-Json -Depth 5
```

## Test du Rapport des Mouvements de Stock
```powershell
# Sans filtres
$headers = @{ Authorization = "Bearer YOUR_TOKEN_HERE" }
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/movements" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Avec filtres de date
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/movements?start_date=2023-01-01&end_date=2023-12-31" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Avec filtres de type
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/movements?type=entry&product_id=1" -Method Get -Headers $headers | ConvertTo-Json -Depth 5
```

## Test du Rapport de Valorisation du Stock
```powershell
# Valorisation actuelle
$headers = @{ Authorization = "Bearer YOUR_TOKEN_HERE" }
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/valuation" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Avec comparaison dans le temps
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/valuation?compare_date=2023-01-01" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Filtré par catégorie
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/valuation?category_id=1" -Method Get -Headers $headers | ConvertTo-Json -Depth 5
```

## Test du Rapport de Rotation des Produits
```powershell
# Période par défaut (3 derniers mois)
$headers = @{ Authorization = "Bearer YOUR_TOKEN_HERE" }
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/turnover" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Période personnalisée
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/turnover?start_date=2023-01-01&end_date=2023-12-31" -Method Get -Headers $headers | ConvertTo-Json -Depth 5

# Filtré par catégorie
Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/turnover?category_id=1" -Method Get -Headers $headers | ConvertTo-Json -Depth 5
```

## Vérification de la Mise en Cache
Pour tester si la mise en cache fonctionne correctement, exécutez la même requête plusieurs fois et comparez le temps de réponse.

```powershell
# Premier appel (devrait être plus lent)
$startTime = Get-Date
$response = Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/inventory" -Method Get -Headers $headers
$endTime = Get-Date
$executionTime = ($endTime - $startTime).TotalMilliseconds
Write-Host "Premier appel: $executionTime ms"

# Deuxième appel (devrait être plus rapide si la mise en cache fonctionne)
$startTime = Get-Date
$response = Invoke-RestMethod -Uri "http://localhost/SFE/api/public/api/reports/inventory" -Method Get -Headers $headers
$endTime = Get-Date
$executionTime = ($endTime - $startTime).TotalMilliseconds
Write-Host "Deuxième appel: $executionTime ms"
```

## Notes
- Ces tests sont conçus pour PowerShell et n'utilisent pas l'opérateur `&&`
- Vous pouvez ajuster les paramètres de filtrage selon vos besoins
- Les tests de performance peuvent varier selon la configuration du serveur et la quantité de données

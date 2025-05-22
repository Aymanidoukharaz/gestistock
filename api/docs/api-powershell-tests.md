# Tests d'API avec PowerShell

Ce document fournit des exemples de commandes PowerShell pour tester l'API GESTISTOCK après les modifications apportées aux ressources.

## Configuration

```powershell
# Définir l'URL de base de l'API
$baseUrl = "http://localhost:8000/api"

# Fonction pour obtenir un token JWT
function Get-AuthToken {
    param (
        [string]$email = "admin@example.com",
        [string]$password = "password"
    )
    
    $loginBody = @{
        email = $email
        password = $password
    } | ConvertTo-Json
    
    $response = Invoke-RestMethod -Uri "$baseUrl/auth/login" -Method Post -Body $loginBody -ContentType "application/json"
    return $response.data.access_token
}

# Obtenir un token d'authentification
$token = Get-AuthToken
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

Write-Host "Token obtenu : $token"
```

## Tests des bons d'entrée

```powershell
# Récupérer la liste des bons d'entrée
$entryForms = Invoke-RestMethod -Uri "$baseUrl/entry-forms" -Method Get -Headers $headers
Write-Host "Nombre de bons d'entrée : $($entryForms.meta.total_count)"
Write-Host "Structure de la réponse : $($entryForms | ConvertTo-Json -Depth 5)"

# Récupérer un bon d'entrée spécifique (remplacer 1 par un ID valide)
$entryForm = Invoke-RestMethod -Uri "$baseUrl/entry-forms/1" -Method Get -Headers $headers
Write-Host "Bon d'entrée #1 : $($entryForm | ConvertTo-Json -Depth 5)"
```

## Tests des bons de sortie

```powershell
# Récupérer la liste des bons de sortie
$exitForms = Invoke-RestMethod -Uri "$baseUrl/exit-forms" -Method Get -Headers $headers
Write-Host "Nombre de bons de sortie : $($exitForms.meta.total_count)"
Write-Host "Structure de la réponse : $($exitForms | ConvertTo-Json -Depth 5)"

# Récupérer un bon de sortie spécifique (remplacer 1 par un ID valide)
$exitForm = Invoke-RestMethod -Uri "$baseUrl/exit-forms/1" -Method Get -Headers $headers
Write-Host "Bon de sortie #1 : $($exitForm | ConvertTo-Json -Depth 5)"
```

## Tests des erreurs

```powershell
# Test d'accès à une ressource inexistante
try {
    $nonExistent = Invoke-RestMethod -Uri "$baseUrl/entry-forms/999" -Method Get -Headers $headers
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    $response = $_.ErrorDetails.Message | ConvertFrom-Json
    Write-Host "Réponse d'erreur pour ressource inexistante : StatusCode=$statusCode, Message=$($response.message)"
}

# Test d'accès sans authentification
try {
    $noAuth = Invoke-RestMethod -Uri "$baseUrl/entry-forms" -Method Get
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    $response = $_.ErrorDetails.Message | ConvertFrom-Json
    Write-Host "Réponse d'erreur pour accès non authentifié : StatusCode=$statusCode, Message=$($response.message)"
}
```

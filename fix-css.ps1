# SMART-HEALTH - Fix CSS & Assets (PowerShell)

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  SMART-HEALTH - Fix CSS & Assets" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Vérifier que les fichiers CSS existent
Write-Host "[1/6] Vérification des fichiers CSS..." -ForegroundColor Yellow

if (Test-Path "public\css\profile-sidebar.css") {
    Write-Host "✓ profile-sidebar.css trouvé" -ForegroundColor Green
} else {
    Write-Host "✗ profile-sidebar.css MANQUANT" -ForegroundColor Red
}

if (Test-Path "public\css\admin-scroll-system.css") {
    Write-Host "✓ admin-scroll-system.css trouvé" -ForegroundColor Green
} else {
    Write-Host "✗ admin-scroll-system.css MANQUANT" -ForegroundColor Red
}

if (Test-Path "public\css\patient-pages.css") {
    Write-Host "✓ patient-pages.css trouvé" -ForegroundColor Green
} else {
    Write-Host "✗ patient-pages.css MANQUANT" -ForegroundColor Red
}

Write-Host ""

# 2. Nettoyer tous les caches
Write-Host "[2/6] Nettoyage des caches Laravel..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
Write-Host "✓ Caches nettoyés" -ForegroundColor Green
Write-Host ""

# 3. Créer le lien symbolique storage
Write-Host "[3/6] Création du lien symbolique storage..." -ForegroundColor Yellow
php artisan storage:link
Write-Host "✓ Lien symbolique créé" -ForegroundColor Green
Write-Host ""

# 4. Vérifier les permissions (Windows - optionnel)
Write-Host "[4/6] Vérification des permissions..." -ForegroundColor Yellow
if (Test-Path "storage") {
    icacls storage /grant Users:F /T /Q >$null 2>&1
    icacls "bootstrap\cache" /grant Users:F /T /Q >$null 2>&1
    Write-Host "✓ Permissions configurées" -ForegroundColor Green
} else {
    Write-Host "⚠ Dossier storage introuvable" -ForegroundColor Yellow
}
Write-Host ""

# 5. Optimiser pour production (optionnel)
$response = Read-Host "Optimiser pour production ? (y/n)"
if ($response -eq "y" -or $response -eq "Y") {
    Write-Host "[5/6] Optimisation pour production..." -ForegroundColor Yellow
    composer install --optimize-autoloader --no-dev
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    Write-Host "✓ Optimisations appliquées" -ForegroundColor Green
} else {
    Write-Host "[5/6] Optimisations ignorées" -ForegroundColor Yellow
}
Write-Host ""

# 6. Résumé
Write-Host "[6/6] Résumé..." -ForegroundColor Yellow
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "✓ Correction terminée" -ForegroundColor Green
Write-Host ""
Write-Host "Prochaines étapes :"
Write-Host "1. Redémarrer le serveur web (IIS/Apache/Nginx)"
Write-Host "2. Vider le cache du navigateur (Ctrl+F5)"
Write-Host "3. Tester l'application"
Write-Host ""
Write-Host "En cas de problème :"
Write-Host "- Consulter storage\logs\laravel.log"
Write-Host "- Vérifier la console du navigateur (F12)"
Write-Host "- Lire GUIDE_DEPLOIEMENT.md"
Write-Host "=========================================" -ForegroundColor Cyan

Write-Host ""
Write-Host "Appuyez sur une touche pour continuer..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")




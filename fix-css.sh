#!/bin/bash

echo "========================================="
echo "  SMART-HEALTH - Fix CSS & Assets"
echo "========================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Vérifier que les fichiers CSS existent
echo -e "${YELLOW}[1/6]${NC} Vérification des fichiers CSS..."
if [ -f "public/css/profile-sidebar.css" ]; then
    echo -e "${GREEN}✓${NC} profile-sidebar.css trouvé"
else
    echo -e "${RED}✗${NC} profile-sidebar.css MANQUANT"
fi

if [ -f "public/css/admin-scroll-system.css" ]; then
    echo -e "${GREEN}✓${NC} admin-scroll-system.css trouvé"
else
    echo -e "${RED}✗${NC} admin-scroll-system.css MANQUANT"
fi

if [ -f "public/css/patient-pages.css" ]; then
    echo -e "${GREEN}✓${NC} patient-pages.css trouvé"
else
    echo -e "${RED}✗${NC} patient-pages.css MANQUANT"
fi

echo ""

# 2. Nettoyer tous les caches
echo -e "${YELLOW}[2/6]${NC} Nettoyage des caches Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✓${NC} Caches nettoyés"
echo ""

# 3. Créer le lien symbolique storage
echo -e "${YELLOW}[3/6]${NC} Création du lien symbolique storage..."
php artisan storage:link
echo -e "${GREEN}✓${NC} Lien symbolique créé"
echo ""

# 4. Vérifier les permissions
echo -e "${YELLOW}[4/6]${NC} Vérification des permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/css
echo -e "${GREEN}✓${NC} Permissions configurées"
echo ""

# 5. Optimiser pour production (optionnel)
read -p "Optimiser pour production ? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]
then
    echo -e "${YELLOW}[5/6]${NC} Optimisation pour production..."
    composer install --optimize-autoloader --no-dev
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo -e "${GREEN}✓${NC} Optimisations appliquées"
else
    echo -e "${YELLOW}[5/6]${NC} Optimisations ignorées"
fi
echo ""

# 6. Résumé
echo -e "${YELLOW}[6/6]${NC} Résumé..."
echo "========================================="
echo -e "${GREEN}✓ Correction terminée${NC}"
echo ""
echo "Prochaines étapes :"
echo "1. Redémarrer le serveur web (Apache/Nginx)"
echo "2. Vider le cache du navigateur (Ctrl+F5)"
echo "3. Tester l'application"
echo ""
echo "En cas de problème :"
echo "- Consulter storage/logs/laravel.log"
echo "- Vérifier la console du navigateur (F12)"
echo "- Lire GUIDE_DEPLOIEMENT.md"
echo "========================================="




# 📋 Fichiers de Déploiement - SMART-HEALTH

Ce document liste tous les fichiers créés pour vous aider à déployer et maintenir la plateforme SMART-HEALTH.

## 📄 Fichiers de Documentation

### 1. `GUIDE_DEPLOIEMENT.md`
**Description :** Guide complet de déploiement avec checklist, optimisations, et résolution de problèmes.

**Utilisez-le pour :**
- Déployer l'application en production
- Optimiser les performances
- Configurer le serveur web (Apache/Nginx)
- Mettre en place des backups automatiques
- Résoudre les problèmes courants

### 2. `FIX_CSS_README.md`
**Description :** Guide spécifique pour résoudre les problèmes de styles CSS (notamment la sidebar).

**Utilisez-le pour :**
- Diagnostiquer pourquoi les styles ne s'affichent pas
- Vérifier la configuration du serveur web
- Tester le chargement des assets CSS
- Résoudre les problèmes de cache

### 3. `README.md`
**Description :** Documentation principale du projet avec fonctionnalités et structure.

**Utilisez-le pour :**
- Comprendre les fonctionnalités de la plateforme
- Voir la structure du projet
- Connaître les technologies utilisées

## 🔧 Scripts d'Aide

### 1. `fix-css.ps1` (Windows PowerShell)
**Description :** Script automatique pour réparer les problèmes CSS sur Windows.

**Utilisation :**
```powershell
.\fix-css.ps1
```

**Ce qu'il fait :**
- Vérifie l'existence des fichiers CSS
- Nettoie tous les caches Laravel
- Crée le lien symbolique storage
- Configure les permissions
- Option d'optimisation pour production

### 2. `fix-css.sh` (Linux/Mac Bash)
**Description :** Script automatique pour réparer les problèmes CSS sur Linux/Mac.

**Utilisation :**
```bash
chmod +x fix-css.sh
./fix-css.sh
```

**Ce qu'il fait :**
- Même fonctionnalités que la version PowerShell
- Adapté pour systèmes Unix

## 🗂️ Structure des Fichiers Importants

```
smart-health/
├── app/
│   ├── Models/
│   │   ├── Evaluation.php ✅ Corrigé (timestamps + relations)
│   │   └── ...
│   ├── Services/
│   │   ├── PayDunyaService.php ✅ Intégration PayDunya
│   │   └── PaymentService.php ✅ Gestion paiements
│   └── Http/
│       └── Controllers/
│           └── PaymentController.php ✅ Contrôleur paiements
├── config/
│   └── services.php ✅ Configuration PayDunya
├── database/
│   └── migrations/
│       ├── 2025_10_22_000000_ensure_timestamps_on_evaluations.php ✅ Fix timestamps
│       └── ...
├── public/
│   └── css/
│       ├── profile-sidebar.css ✅ Styles sidebar
│       ├── admin-scroll-system.css ✅ Styles admin
│       └── patient-pages.css ✅ Styles patient
├── resources/
│   └── views/
│       ├── admin/
│       │   └── evaluations.blade.php ✅ Corrigé (diffForHumans)
│       ├── layouts/
│       │   └── partials/
│       │       └── profile_sidebar.blade.php ✅ Sidebar modernisée
│       └── patient/
│           └── paiements.blade.php ✅ PayDunya uniquement
├── routes/
│   └── web.php ✅ Routes PayDunya
├── .env ✅ Configuration environnement
├── GUIDE_DEPLOIEMENT.md 📘 Guide de déploiement
├── FIX_CSS_README.md 📘 Guide fix CSS
├── DEPLOIEMENT_FICHIERS.md 📘 Ce fichier
├── fix-css.ps1 🔧 Script Windows
└── fix-css.sh 🔧 Script Linux/Mac
```

## ✅ Checklist de Déploiement Rapide

### Avant le déploiement

- [ ] Tester localement avec `php artisan serve`
- [ ] Vérifier que tous les fichiers CSS sont présents dans `public/css/`
- [ ] Configurer le fichier `.env` pour la production
- [ ] Générer la clé d'application : `php artisan key:generate`
- [ ] Exécuter les migrations : `php artisan migrate --force`

### Pendant le déploiement

- [ ] Copier tous les fichiers du projet sur le serveur
- [ ] Configurer le serveur web (Apache/Nginx) pour pointer vers `/public`
- [ ] Installer les dépendances : `composer install --optimize-autoloader --no-dev`
- [ ] Créer le lien symbolique : `php artisan storage:link`
- [ ] Configurer les permissions : `chmod -R 755 storage bootstrap/cache`

### Après le déploiement

- [ ] Vider tous les caches : `php artisan cache:clear`
- [ ] Optimiser pour production : `php artisan optimize`
- [ ] Tester l'application dans le navigateur
- [ ] Vérifier que les styles s'affichent correctement
- [ ] Tester les paiements en mode sandbox
- [ ] Configurer les backups automatiques

## 🚨 En cas de problème

1. **Les styles ne s'affichent pas**
   - Lire `FIX_CSS_README.md`
   - Exécuter `fix-css.ps1` ou `fix-css.sh`

2. **Erreur 500**
   - Consulter `storage/logs/laravel.log`
   - Vérifier les permissions
   - Activer temporairement `APP_DEBUG=true` dans `.env`

3. **Erreur de base de données**
   - Vérifier `php artisan migrate:status`
   - Exécuter `php artisan migrate --force`

4. **Paiements qui ne fonctionnent pas**
   - Vérifier les clés PayDunya dans `.env`
   - Tester en mode sandbox d'abord (`PAYMENTS_SANDBOX=true`)
   - Consulter les logs PayDunya

## 📞 Ressources Utiles

- **Documentation Laravel :** https://laravel.com/docs
- **Documentation PayDunya :** https://paydunya.com/developers
- **Bootstrap Icons :** https://icons.getbootstrap.com
- **Stack Overflow :** https://stackoverflow.com/questions/tagged/laravel

---

**Date de création :** 2025-10-22  
**Plateforme :** SMART-HEALTH  
**Version Laravel :** 12.x  
**Version PHP :** 8.2+




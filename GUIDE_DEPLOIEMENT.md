# 🚀 Guide de Déploiement - SMART-HEALTH

## ✅ Checklist avant déploiement

### 1. **Vérifications des Assets CSS/JS**

Si les styles de la sidebar ou d'autres éléments ne s'affichent pas correctement :

```bash
# Vérifier que les fichiers CSS existent
ls public/css/

# Nettoyer tous les caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Créer le lien symbolique pour storage
php artisan storage:link

# Si vous avez Node.js installé, compiler les assets
npm install
npm run build
```

### 2. **Configuration de l'environnement (.env)**

Assurez-vous que votre fichier `.env` est correctement configuré :

```env
APP_NAME="SMART-HEALTH"
APP_ENV=production
APP_KEY=base64:... # Généré avec php artisan key:generate
APP_DEBUG=false # IMPORTANT: false en production
APP_URL=https://votre-domaine.com

DB_CONNECTION=sqlite
DB_DATABASE=/chemin/absolu/vers/database.sqlite

# Configuration PayDunya
PAYDUNYA_MASTER_KEY=votre_master_key
PAYDUNYA_PUBLIC_KEY=votre_public_key
PAYDUNYA_PRIVATE_KEY=votre_private_key
PAYDUNYA_TOKEN=votre_token
PAYDUNYA_MODE=live # 'test' pour sandbox, 'live' pour production

# Configuration Email
MAIL_MAILER=smtp
MAIL_HOST=votre_serveur_smtp
MAIL_PORT=587
MAIL_USERNAME=votre_email
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@smart-health.com
MAIL_FROM_NAME="SMART-HEALTH"

# Sandbox pour paiements (false en production)
PAYMENTS_SANDBOX=false
```

### 3. **Permissions des fichiers**

```bash
# Sur Linux/Mac
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Sur Windows (PowerShell en tant qu'admin)
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### 4. **Migrations de base de données**

```bash
# Vérifier le statut des migrations
php artisan migrate:status

# Exécuter les migrations manquantes
php artisan migrate --force

# En cas de problème, rafraîchir (⚠️ SUPPRIME TOUTES LES DONNÉES!)
php artisan migrate:fresh --seed --force
```

### 5. **Optimisations pour production**

```bash
# Optimiser l'autoloader de Composer
composer install --optimize-autoloader --no-dev

# Mettre en cache la configuration
php artisan config:cache

# Mettre en cache les routes
php artisan route:cache

# Mettre en cache les vues
php artisan view:cache

# Optimiser tout en une seule commande
php artisan optimize
```

## 🔧 Résolution de problèmes courants

### Problème : Les styles CSS ne se chargent pas

**Solution 1 : Vérifier les chemins**
```bash
# Vérifier que les fichiers existent
ls -la public/css/profile-sidebar.css
ls -la public/css/admin-scroll-system.css
ls -la public/css/patient-pages.css
```

**Solution 2 : Vérifier la configuration du serveur web**

Pour **Apache** (`.htaccess` dans `public/`) :
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

Pour **Nginx** (`/etc/nginx/sites-available/smart-health`) :
```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /chemin/vers/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache des assets statiques
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Solution 3 : Forcer le rechargement**
- Ouvrez la console du navigateur (F12)
- Faites `Ctrl + F5` pour forcer le rechargement
- Vérifiez s'il y a des erreurs 404 dans l'onglet Network

### Problème : Erreur 500 Internal Server Error

```bash
# Activer temporairement le debug
# Dans .env : APP_DEBUG=true

# Consulter les logs
tail -f storage/logs/laravel.log

# Vérifier les permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Problème : Base de données locked (SQLite)

```bash
# Vérifier les processus qui utilisent la base
lsof database/database.sqlite

# Redémarrer PHP-FPM
sudo systemctl restart php8.2-fpm

# Ou utiliser MySQL/PostgreSQL en production (recommandé)
```

## 📊 Monitoring et Maintenance

### Logs

```bash
# Voir les dernières erreurs
tail -50 storage/logs/laravel.log

# Suivre les logs en temps réel
tail -f storage/logs/laravel.log

# Nettoyer les vieux logs
rm storage/logs/laravel-*.log
```

### Backup automatique

Créez un script `backup.sh` :
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/chemin/vers/backups"

# Backup base de données
cp database/database.sqlite "$BACKUP_DIR/db_$DATE.sqlite"

# Backup fichiers uploadés
tar -czf "$BACKUP_DIR/storage_$DATE.tar.gz" storage/app/public

# Garder seulement les 7 derniers backups
find $BACKUP_DIR -name "db_*.sqlite" -mtime +7 -delete
find $BACKUP_DIR -name "storage_*.tar.gz" -mtime +7 -delete

echo "Backup effectué: $DATE"
```

Ajoutez au crontab :
```bash
crontab -e
# Ajouter cette ligne pour backup quotidien à 2h du matin
0 2 * * * /chemin/vers/backup.sh >> /var/log/smart-health-backup.log 2>&1
```

## 🔒 Sécurité en Production

1. **Toujours** `APP_DEBUG=false`
2. **Toujours** utiliser HTTPS
3. **Jamais** commiter le `.env`
4. Garder Laravel et dépendances à jour : `composer update`
5. Utiliser des mots de passe forts pour la base de données
6. Configurer un pare-feu (UFW, iptables)
7. Limiter l'accès SSH
8. Configurer les sauvegardes automatiques

## 📞 Support

En cas de problème persistant :
1. Consultez les logs : `storage/logs/laravel.log`
2. Vérifiez la configuration du serveur web
3. Testez en local d'abord
4. Consultez la documentation Laravel : https://laravel.com/docs

---

**Date de création:** {{ date('Y-m-d') }}  
**Version:** 1.0




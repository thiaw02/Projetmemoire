# Guide d'Installation et Configuration Redis pour Windows

## 📋 Prérequis

Redis est maintenant configuré dans votre application Laravel, mais vous devez l'installer sur votre système Windows pour qu'il fonctionne correctement.

## 🚀 Installation Redis sur Windows

### Option 1 : Utiliser Windows Subsystem for Linux (WSL) - Recommandé

1. **Installer WSL2** (si pas déjà fait) :
   ```powershell
   wsl --install
   ```

2. **Installer Redis dans WSL** :
   ```bash
   # Dans le terminal WSL
   sudo apt update
   sudo apt install redis-server
   ```

3. **Démarrer Redis** :
   ```bash
   sudo service redis-server start
   ```

4. **Vérifier l'installation** :
   ```bash
   redis-cli ping
   # Devrait retourner : PONG
   ```

### Option 2 : Redis pour Windows (Community)

1. **Télécharger Redis** :
   - Aller sur : https://github.com/microsoftarchive/redis/releases
   - Télécharger la dernière version (ex: Redis-x64-3.0.504.msi)

2. **Installer Redis** :
   - Exécuter le fichier MSI
   - Suivre les instructions d'installation
   - Cocher "Add the Redis installation folder to the PATH environment variable"

3. **Démarrer Redis** :
   ```cmd
   redis-server
   ```

### Option 3 : Docker (Alternative moderne)

1. **Installer Docker Desktop pour Windows**

2. **Lancer Redis dans un conteneur** :
   ```bash
   docker run -d --name redis-server -p 6379:6379 redis:alpine
   ```

## ⚙️ Configuration Laravel

Votre fichier `.env` a été automatiquement configuré avec :

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🔧 Configuration Avancée Redis

### 1. Optimiser Redis pour les performances

Créer un fichier de configuration Redis personnalisé :

```bash
# redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### 2. Sécuriser Redis

```bash
# Dans redis.conf
requirepass your_secure_password
bind 127.0.0.1
```

Puis mettre à jour votre `.env` :
```env
REDIS_PASSWORD=your_secure_password
```

## 🧪 Tester la Configuration

### 1. Test via Laravel Tinker

```bash
php artisan tinker
```

```php
// Dans Tinker
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

// Test du cache
Cache::put('test', 'Hello Redis!', 60);
Cache::get('test'); // Devrait retourner "Hello Redis!"

// Test direct Redis
Redis::set('direct_test', 'Direct Redis access');
Redis::get('direct_test'); // Devrait retourner "Direct Redis access"
```

### 2. Test des sessions

1. Connectez-vous à votre application
2. Vérifiez dans Redis CLI :
   ```bash
   redis-cli
   KEYS *
   ```

## 📊 Monitoring Redis

### Commandes utiles

```bash
# Statistiques Redis
redis-cli info

# Voir toutes les clés
redis-cli keys "*"

# Monitorer en temps réel
redis-cli monitor

# Voir l'utilisation mémoire
redis-cli info memory
```

### Interface graphique (optionnel)

1. **Redis Desktop Manager** : https://resp.app/
2. **Redis Commander** (web-based) :
   ```bash
   npm install -g redis-commander
   redis-commander
   ```

## 🚨 Dépannage

### Problèmes courants

1. **Redis ne démarre pas** :
   - Vérifier le port 6379 n'est pas utilisé
   - Vérifier les permissions de fichier

2. **Laravel ne peut pas se connecter** :
   ```bash
   # Tester la connexion
   telnet 127.0.0.1 6379
   
   # Vérifier les logs Laravel
   tail -f storage/logs/laravel.log
   ```

3. **Erreur de mémoire** :
   - Augmenter `maxmemory` dans redis.conf
   - Vérifier la politique d'éviction

### Logs Redis

```bash
# Trouver le fichier de log Redis
redis-cli config get logfile

# Sur Windows (installation MSI)
# Le log est généralement dans : C:\Program Files\Redis\Logs\redis-server.log
```

## 🔄 Démarrage automatique

### Windows Service

1. **Installer Redis comme service** (si installé via MSI, c'est automatique)
2. **Démarrer le service** :
   ```cmd
   net start Redis
   ```

### WSL

Ajouter à `.bashrc` ou `.profile` :
```bash
# Auto-start Redis
if ! pgrep -x redis-server > /dev/null; then
    sudo service redis-server start
fi
```

## 📈 Optimisation des performances

### Configuration Laravel pour Redis

Dans `config/database.php`, optimiser la configuration Redis :

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'), // Plus rapide que 'predis'
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
        'read_write_timeout' => 60,
        'context' => [
            // Options SSL si nécessaire
        ],
    ],
    // ... autres configurations
],
```

## ✅ Vérification finale

Une fois Redis installé et configuré :

1. **Redémarrer votre application Laravel**
2. **Vider le cache** :
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

3. **Tester le monitoring des performances** :
   - Aller sur `/admin/performance`
   - Vérifier que le status du cache montre "redis" et "OK"

## 📞 Support

Si vous rencontrez des problèmes :

1. Vérifier les logs Laravel : `storage/logs/laravel.log`
2. Vérifier les logs Redis
3. Tester la connectivité : `php artisan tinker` puis `Redis::ping()`

Redis est maintenant prêt à booster les performances de votre application de santé ! 🚀
# Guide du Système de Paiement Sandbox

## Vue d'ensemble

Le système de paiement sandbox permet de tester les fonctionnalités de paiement sans traiter de vrais paiements. Il est automatiquement activé en environnement de développement (`local`) et peut être utilisé pour simuler des transactions Wave et Orange Money.

## Configuration

### Variables d'environnement (`.env`)
```bash
APP_ENV=local                # Environnement (local/production)
PAYMENTS_SANDBOX=true        # Active le mode sandbox
```

### Comment ça marche
- Quand `APP_ENV != production` ET `PAYMENTS_SANDBOX=true`, tous les paiements sont redirigés vers la page sandbox
- Au lieu d'appeler les APIs réelles (Wave/Orange Money), une URL sandbox locale est générée
- L'utilisateur peut simuler succès ou échec du paiement

## Flux de paiement Sandbox

### 1. Initiation du paiement
Quand un patient clique sur "Payer" :

```php
// Dans PaymentController@checkout
$order = Order::create([...]);
$paymentService = new PaymentService();
$order = $paymentService->createCheckout($order, 'wave');
// Redirige vers $order->payment_url (sandbox)
```

### 2. Page Sandbox
URL : `/payments/sandbox/{order_id}`

**Interface utilisateur :**
- Affiche les détails de la commande (montant, items)
- Badge indiquant le provider choisi (Wave/Orange Money)
- Deux boutons de simulation :
  - "Simuler succès" → `/payments/success?order={id}`
  - "Simuler échec" → `/payments/cancel?order={id}`

### 3. Callbacks de simulation

**Succès** (`/payments/success`) :
- Change le statut : `pending → paid`
- Définit `paid_at` à l'horodatage actuel  
- Envoie un reçu par email (si configuré)
- Redirige vers le dashboard patient avec message de succès

**Échec** (`/payments/cancel`) :
- Change le statut : `pending → canceled`
- Redirige vers le dashboard patient avec message d'annulation

## Utilisation pratique

### Pour tester un paiement :

1. **Se connecter** comme patient
2. **Aller** sur `/patient/paiements` 
3. **Choisir** un type de paiement (consultation, analyse, acte, rdv)
4. **Sélectionner** Wave ou Orange Money
5. **Cliquer** "Payer" → Redirection automatique vers sandbox
6. **Simuler** le résultat souhaité

### URLs importantes :
```
Page paiements patient : /patient/paiements
Page sandbox : /payments/sandbox/{order_id}
Callback succès : /payments/success?order={id}
Callback échec : /payments/cancel?order={id}
Télécharger reçu : /payments/{order_id}/receipt
```

## Données de test disponibles

Le système contient :
- **107 patients** générés avec le seeder
- **341 commandes** (dont 64 pending, 243 payées)
- **Patient test** : Bineta Sarr (bineta.sarr1@patient.com)

## Structure des données

### Table `orders`
```sql
id, user_id, patient_id, currency, total_amount, status, 
provider, provider_ref, payment_url, paid_at, metadata
```

### Table `order_items`  
```sql
id, order_id, item_type, item_id, label, amount, ticket_number
```

### Statuts possibles
- `pending` : En attente de paiement
- `paid` : Payé avec succès  
- `canceled` : Annulé par l'utilisateur
- `failed` : Échec du paiement

## Fonctionnalités avancées

### Génération de reçus
- Reçus PDF générés automatiquement après paiement réussi
- Téléchargement via `/payments/{order_id}/receipt`
- Template moderne avec logo et informations de l'hôpital

### Notifications email
- Envoi automatique du reçu après paiement réussi
- Configuration dans `config/mail.php`
- Mode `log` pour développement (emails dans `storage/logs/laravel.log`)

### Sécurité
- Vérification de propriété des commandes
- Middleware d'authentification sur toutes les routes de paiement
- Protection contre les accès non autorisés aux reçus

## Passage en production

Pour activer les vrais paiements :

1. **Configurer** les clés API dans `.env` :
```bash
WAVE_API_KEY=your_wave_api_key
WAVE_API_SECRET=your_wave_api_secret
ORANGE_MONEY_API_KEY=your_orange_api_key  
ORANGE_MONEY_API_SECRET=your_orange_api_secret
```

2. **Définir** l'environnement :
```bash
APP_ENV=production
PAYMENTS_SANDBOX=false
```

3. **Implémenter** les méthodes réelles dans `PaymentService` :
   - `createWave()` : Intégration API Wave
   - `createOrangeMoney()` : Intégration API Orange Money

## Dépannage

### Problème : "Page sandbox non accessible"
**Solution :** Vérifier que l'utilisateur est authentifié et propriétaire de la commande

### Problème : "Paiement ne fonctionne pas"
**Solution :** Vérifier `PAYMENTS_SANDBOX=true` dans `.env`

### Problème : "Reçu non généré"
**Solution :** Vérifier l'installation de DomPDF : `composer require barryvdh/laravel-dompdf`

## Support

Le système sandbox est entièrement fonctionnel et prêt pour les tests. Toutes les fonctionnalités principales sont implémentées :

- ✅ Interface de paiement patient
- ✅ Simulation sandbox complète  
- ✅ Gestion des callbacks succès/échec
- ✅ Génération de reçus PDF
- ✅ Notifications email
- ✅ Sécurité et autorisations
- ✅ Données de test richess

L'architecture est extensible et prête pour l'intégration des APIs de paiement réelles en production.
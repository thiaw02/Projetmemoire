# SMART-HEALTH (sys_infor_medic)

Ce projet est une application Laravel 12 destinée à la gestion complète d'un établissement de santé avec système de paiement intégré.

Cette version majeure apporte des correctifs importants et de nouvelles fonctionnalités avancées pour l'administration, les secrétaires, médecins, infirmiers et patients, avec un focus particulier sur la gestion financière.

## Nouvelles fonctionnalités et changements principaux

### 💳 **Système de paiement intégré - NOUVEAU !**
- Dashboard secrétaire avec onglets (Vue d'ensemble, Paiements, Actions rapides)
- Gestion complète des paiements (Wave, Orange Money, Free Money)
- KPIs financiers en temps réel (montants mensuels, paiements en attente)
- Export CSV/PDF des transactions
- Génération automatique de liens de paiement
- Quittances numériques avec QR codes
- Paramètres de tarification configurable

### 📊 **Tableaux de bord améliorés**
- Dashboard administrateur avec graphiques interactifs (Chart.js)
- Statistiques temps réel : volumes mensuels, rendez-vous par statut
- Dashboard secrétaire restructuré avec interface à onglets
- KPIs visuels pour le suivi des performances

### 👨‍⚕️ **Accès médecin**
- Consultation des dossiers patients (constantes infirmier, consultations, ordonnances, analyses) depuis un écran dédié
- Liste des RDV confirmés à venir avec actions rapides (ouvrir dossier, créer consultation, rédiger ordonnance) et filtres jour/semaine/tous
- Marquer un RDV comme consulté (statut synchronisé sur "terminé")

### 👩‍⚕️ **Accès infirmier**
- Affichage des prochains rendez-vous dans le tableau de bord infirmier

### 📝 **Ordonnances (médecin et patient)**
- Saisie multi-lignes des médicaments (affichés sous forme de liste à puces) + dosage global
- Téléchargement de l'ordonnance (PDF via DomPDF si installé, sinon HTML fallback)
- Envoi automatique de l'ordonnance par e‑mail au patient à la création
- Boutons pour renvoyer l'ordonnance par e‑mail (côté médecin et côté patient)

### 📋 **Accès secrétaire - AMÉLIORÉ !**
- Interface dashboard à onglets moderne (Vue d'ensemble, Paiements, Actions rapides)
- Accès complet à la gestion des paiements avec KPIs financiers
- Visualisation des 20 derniers paiements avec actions contextuelles
- Export des données de paiement en CSV
- Gestion des paramètres de tarification
- Création et suivi des liens de paiement

- Rappels RDV par e‑mail (Scheduler)
  - Notification envoyée le jour J (07:00) et la veille (08:00).
  - Basé sur le Scheduler Laravel (artisan schedule:run).

- Administration des comptes
  - Activation/Désactivation des comptes (admin, secrétaire, médecin, infirmier, patient).
  - Contrôle à la connexion: un compte inactif ne peut pas se connecter.

- Audit log (journal d’audit)
  - journalisation des mises à jour de consultations et d’ordonnances (avant/après), création d’ordonnance, et des bascules Actif/Inactif.
  - UI dédiée pour consulter les logs: filtre par utilisateur et recherche.

- UI/UX Administration
  - Listes revisitées: couleurs sur les actions (modifier/supprimer), boutons regroupés, filtres plus clairs.
  - Superviser rôles: passage à une icône d’action avec fenêtre modale, sélection du rôle et confirmation + toast de succès.
  - Gestion des rôles & permissions simplifiée: une seule carte "Accès indispensables" avec niveaux d’accès par rôle (Aucun / Lecture / Complet) pour les fonctionnalités clés.

## Installation rapide

### 1) Dépendances système

- PHP 8.2+
- Composer
- Node.js (pour Vite si nécessaire)
- Extension PHP curl (pour les paiements)
- Extension PHP gd (pour les QR codes)

### 2) Installation Laravel

```bash
# Installer les dépendances
composer install

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations et seeders
php artisan migrate
php artisan db:seed
```

### 3) Configuration des paiements (.env)

```bash
# Configuration Wave (Senegal)
WAVE_API_KEY=votre_cle_api_wave
WAVE_SECRET_KEY=votre_secret_wave
WAVE_MERCHANT_ID=votre_merchant_id

# Configuration Orange Money
ORANGE_API_KEY=votre_cle_api_orange
ORANGE_MERCHANT_KEY=votre_merchant_orange

# Configuration Free Money  
FREE_API_KEY=votre_cle_api_free
FREE_MERCHANT_ID=votre_merchant_free

# URLs de callback
PAYMENT_SUCCESS_URL=http://localhost:8000/payments/success
PAYMENT_CANCEL_URL=http://localhost:8000/payments/cancel
```

### 4) DomPDF (optionnel, recommandé pour les PDF)

```bash
composer require barryvdh/laravel-dompdf
```

4) Scheduler (rappels RDV)

- Assurer l’exécution régulière du scheduler (chaque minute)

Windows (Planificateur de tâches) :
```
php C:\Users\thiaw\monProjetLaravel\sys_infor_medic\artisan schedule:run
```
Linux/macOS (crontab) :
```
* * * * * php /chemin/vers/sys_infor_medic/artisan schedule:run >> /dev/null 2>&1
```

5) Configuration e‑mail (.env)

Renseigner les variables MAIL_* (MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS, MAIL_FROM_NAME) pour l’envoi des e‑mails (rappels RDV, ordonnances).

## Commandes utiles

- Nettoyer le cache Laravel
```
php artisan optimize:clear
```

- Lancer le serveur de dev
```
php artisan serve
```

## Parcours utilisateur – résumé

### 👤 **Patient**
- Espace Patient: voir dossier médical, consulter/renvoyer les ordonnances, voir/planifier les RDV
- Accès aux quittances de paiement et historique financier
- Réception automatique des liens de paiement par e-mail

### 📋 **Secrétaire**
- **Dashboard moderne à onglets** :
  - 📈 Vue d'ensemble : Statistiques et graphiques
  - 💳 Paiements : KPIs financiers, transactions récentes, exports
  - ⚡ Actions rapides : Accès direct aux fonctionnalités principales
- Gestion complète des paiements (création, suivi, quittances)
- Configuration des tarifs et paramètres de paiement

### 👩‍⚕️ **Infirmier**
- Tableau de bord: suivis récents, dossiers à mettre à jour, prochains RDV

### 👨‍⚕️ **Médecin**
- Dossiers patients: accès à l'historique (constantes, ordonnances, analyses), actions rapides
- Ordonnances: création, téléchargement, envoi par e‑mail, édition

### 🔧 **Administration**
- **Dashboard avec graphiques interactifs** (Chart.js) : volumes mensuels, statistiques en temps réel
- Gestion des utilisateurs: activation/désactivation, liste avancée, export CSV
- Journal d'audit: suivi des modifications clés (consultations, ordonnances, statut utilisateur)
- Rôles et permissions: carte "Indispensables" avec niveaux d'accès (Aucun/Lecture/Complet)
- Supervision financière : vue globale des paiements et revenus

## Notes techniques

### 🚀 **Stack technique**
- **Backend** : Laravel 12 (PHP ^8.2)
- **Frontend** : Bootstrap 5, Bootstrap Icons, Chart.js pour les graphiques
- **Base de données** : MySQL/PostgreSQL avec migrations structurées
- **Paiements** : Intégration Wave, Orange Money, Free Money
- **PDF** : DomPDF pour la génération de documents
- **QR Codes** : Génération automatique pour les quittances

### 🔒 **Sécurité et permissions**
- Middleware de rôles : admin, secretaire, medecin, infirmier, patient
- Authentification sécurisée avec gestion des sessions
- Contrôle d'accès granulaire par fonctionnalité
- Validation côté serveur et client

### 📊 **Fonctionnalités avancées**
- Notifications Laravel pour e‑mails automatiques
- Scheduler Laravel pour tâches planifiées (rappels RDV)
- Journalisation via table audit_logs avec traçabilité complète
- Dashboard interactifs avec graphiques temps réel
- Export de données en CSV/PDF
- Interface responsive et accessible

## 🔄 **Dernières mises à jour (Octobre 2024)**

- ✅ **Correction des routes secrétaire** : Résolution de l'erreur RouteNotFoundException
- ✅ **Dashboard administrateur** : Correction des graphiques Chart.js (volumes mensuels, rendez-vous par statut)
- ✅ **Dashboard secrétaire** : Transformation en interface à onglets avec section paiements
- ✅ **Système de paiement** : Intégration complète avec KPIs et exports
- ✅ **Interface utilisateur** : Amélioration de l'UX avec graphiques interactifs

## 🎨 **Roadmap et extensions possibles**

### 🔮 **Prochaines fonctionnalités**
- 🧪 Module Laboratoire avec gestion des analyses
- 💊 Module Pharmacie avec gestion des stocks
- 📈 Reporting avancé avec tableaux de bord personnalisables
- 📱 Application mobile pour les patients
- 🤖 Intégration IA pour aide au diagnostic

### 🛠️ **Améliorations techniques**
- API REST complète pour intégrations tierces
- Système de sauvegarde automatisée
- Notifications push et SMS
- Interface d'administration avancée

## 📞 **Support et contact**

Pour toute question, demande d'évolution ou support technique :
- 🐛 **Issues** : Créez une issue GitHub pour les bugs
- ✨ **Features** : Proposez vos idées d'amélioration
- 👥 **Contributions** : Les pull requests sont les bienvenues

**🚀 Version actuelle : 2.0 - Edition Paiements Intégrés**

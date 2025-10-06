# SMART-HEALTH — Plateforme médico-administrative (Laravel)

Ce projet est une application web Laravel multi-rôle (admin, médecin, secrétaire, infirmier, patient) permettant la gestion des profils, des patients, des rendez-vous, des consultations, des ordonnances, des analyses, des admissions et une messagerie interne avec pièces jointes.


## Sommaire
- Aperçu des fonctionnalités
- Architecture du projet
- Modèles de données principaux
- Flux d’authentification et rôles
- Messagerie (chat)
- Dépendances et prérequis
- Installation et démarrage (dev)
- Configuration (env)
- Migrations importantes
- Sécurité (CSRF, Auth, Middleware)
- Tests rapides / Débogage

---

## Aperçu des fonctionnalités
- Authentification et réinitialisation de mot de passe (Laravel)
- Profils utilisateurs avec avatar, champs spécifiques par rôle
- Gestion des patients: dossier, numéro de dossier, documents joints
- Rendez-vous: création côté patient, gestion côté secrétaire, statuts, tableaux de bord
- Consultations, ordonnances, analyses, admissions
- Chat interne role-aware (admin↔secrétaire, médecin↔secrétaire/infirmier, infirmier↔secrétaire/médecin, patient↔secrétaire). Fichiers, images, badge non-lus, “en train d’écrire…”, accusés de lecture
- Tableaux de bord par rôle avec statistiques et recherches simples
- Sidebar profil et bouton flottant de messagerie partout (auth)

## Architecture du projet
- Backend: Laravel 10+ (PHP 8.1+ recommandé)
- Front: Blade + Bootstrap 5 + Bootstrap Icons, un peu de JS vanilla
- Dossiers clés:
  - app/Http/Controllers: logique applicative (AdminController, MedecinController, SecretaireController, InfirmierController, PatientController, ChatController, ProfileController, AuthController)
  - app/Models: User, Patient, Rendez_vous, Consultations, Ordonnances, Analyses, Admissions, Conversation, Message, PatientDocument, Setting, RolePermission
  - resources/views: vues Blade organisées par rôle + auth + chat + layouts(partials)
  - database/migrations: schéma (users, patients, rendez-vous, consultations, chat, etc.)
  - database/seeders: DemoDataSeeder pour données de démo

## Modèles de données principaux
- User: name, email, password, role (admin, medecin, infirmier, secretaire, patient), specialite, pro_phone, matricule, cabinet, horaires, avatar_url
- Patient: user_id, secretary_user_id (optionnel), numero_dossier, identité, téléphone, groupe_sanguin, antécédents
- Rendez_vous: user_id (patient user id), medecin_id (users.id), date, heure, motif, statut
- Consultations: patient_id, medecin_id, date_consultation, symptomes, diagnostic, traitement, statut
- Ordonnances, Analyses, Admissions: reliées au patient et parfois médecin
- Conversation, Message: chat avec fichiers et timestamps pour typing/read
- PatientDocument: patient_id, label, type, file_path, uploaded_by
- Setting: key, value pour paramètres globaux

## Flux d’authentification et rôles
- Login classique (email/mot de passe) et redirection selon rôle vers le dashboard correspondant
- Inscription patient publique (génère un mot de passe et envoie un mail de confirmation)
- Middleware auth obligatoire sur tous les dashboards et la plupart des routes
- Middleware role (si nécessaire) pour restreindre certaines sections

## Messagerie (chat)
- Règles:
  - Admin ↔ Secrétaire
  - Médecin ↔ Secrétaire/Infirmier
  - Infirmier ↔ Secrétaire/Médecin
  - Patient ↔ Secrétaire (désormais toutes les secrétaires sont visibles par le patient)
- Fonctionnalités: pagination, fichiers (pdf/images), badge non-lus, typing indicator, read receipts, notifications email

## Dépendances et prérequis
- PHP 8.1+
- Composer
- MySQL/MariaDB ou autre SGBD compatible Laravel
- Node.js (facultatif si vous souhaitez builder des assets additionnels)
- Extensions PHP usuelles: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, Fileinfo

Packages Laravel utilisés (intégrés au framework):
- laravel/framework
- symfony/* et illuminate/* sous-jacents

## Installation et démarrage
1. Cloner le repo
2. Copier .env.example en .env et configurer DB_*, MAIL_*, APP_URL
3. Installer les dépendances Composer
   composer install
4. Générer la clé d’application
   php artisan key:generate
5. Lancer les migrations
   php artisan migrate
6. (Optionnel) Seed de démo
   php artisan db:seed --class=DemoDataSeeder
7. Lier le storage public
   php artisan storage:link
8. Démarrer le serveur
   php artisan serve

## Configuration (.env)
- APP_NAME, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION, MAIL_FROM_ADDRESS, MAIL_FROM_NAME
- SESSION_DOMAIN (si sous-domaines), SANCTUM et CSRF selon besoins

## Migrations importantes (exemples)
- 0001_..._create_users_table: base users + role + specialite (nullable)
- 2025_10_04_000002_add_avatar_url_to_users_table: avatar_url
- 2025_10_04_000005_add_role_specific_fields_to_users: pro_phone, matricule, cabinet, horaires
- Patients: secretary_user_id, numero_dossier
- Chat: conversations, messages (+ typing, fichiers)

## Sécurité (CSRF, Auth, Middleware)
- CSRF: @csrf dans les formulaires + meta CSRF en layout et en-têtes pour fetch
- Auth: middleware('auth') protège tous les dashboards, redirection vers login si non authentifié
- Role: middleware('role') disponible si règles fines nécessaires

## Tests rapides / Débogage
|- Vider caches si vous modifiez les vues/configs:
|  php artisan view:clear
|  php artisan cache:clear
|  php artisan route:clear
|  php artisan config:clear
|- Logs: storage/logs/laravel.log
|
|---
|
|## Mises à jour récentes (journal)
|
|2025-10-06
|- Chat
|  - Envoi automatique des messages vocaux dès l'arrêt de l'enregistrement (interface web type WhatsApp).
|  - Correction d'une erreur SQL (colonne body NOT NULL) pour les messages audio: body par défaut à "" (chaîne vide) quand seul un fichier est envoyé.
|  - Modèle Message mis à jour pour accepter file_path et file_type (mass assignment).
|  - Alignement des règles d'autorisation de l'envoi sur la logique globale: échanges Médecin ↔ Infirmier autorisés; Patient ↔ Secrétaire; Admin ↔ Secrétaire.
|- Inscription
|  - Simplification de la page: affichage direct du formulaire avec contour vert; suppression du panneau de présentation et du CTA intermédiaire.
|- Secrétariat (rappel)
|  - Boutons 2/6/12 mois pour filtrer dynamiquement les graphiques (rendez-vous/admissions). Vider le cache des vues si nécessaire: php artisan view:clear.
|
|---
|
|Pour la documentation plus détaillée (architecture étendue, roadmap, etc.), voir aussi readme/README.md.

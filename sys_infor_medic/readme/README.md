# SMART-HEALTH — Documentation détaillée (Architecture & Dépendances)

Ce document décrit l’architecture technique de la plateforme SMART-HEALTH, ses dépendances, son schéma de données, ses composants (contrôleurs, modèles, vues, middlewares), ainsi que les étapes d’installation et de configuration pour la faire fonctionner en développement.


## 1) Aperçu fonctionnel

- Multi-rôle: administrateur, médecin, secrétaire, infirmier, patient
- Authentification + réinitialisation de mot de passe
- Profils avec avatar + champs spécifiques selon le rôle
- Gestion patients: dossier médical, numéro de dossier, documents
- Rendez-vous: demande côté patient, gestion côté secrétaire, statuts
- Consultations, ordonnances, analyses, admissions
- Chat interne role-aware (règles d’accès), fichiers, notifications email, typing, accusés de lecture
- Dashboards par rôle avec statistiques et recherches
- Sidebar profil et bouton flottant de messagerie partout (pages authentifiées)


## 2) Pile technologique et dépendances

- Langage: PHP 8.1+
- Framework: Laravel 10+
- Base de données: MySQL/MariaDB (ou autre supporté par Laravel)
- Gestion des dépendances: Composer
- Frontend: Blade + Bootstrap 5 (CDN) + Bootstrap Icons (CDN) + JS vanilla
- Email/Notifications: Mécanismes natifs Laravel (Mailables/Notifications)

Extensions PHP recommandées:
- OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, Fileinfo

Outils recommandés:
- Node.js (optionnel) si vous souhaitez builder des assets additionnels

Remarques:
- Aucune dépendance frontend lourde n’est imposée (Bootstrap et icons via CDN)
- FullCalendar est chargé en CDN dans le layout, mais l’onglet RDV patient utilise désormais “Prochain RDV + stats” (le calendrier n’est plus indispensable côté patient)


## 3) Structure des répertoires (extraits pertinents)

- app/
  - Http/
    - Controllers/
      - AdminController.php
      - AuthController.php
      - ChatController.php
      - InfirmierController.php
      - MedecinController.php
      - PatientController.php
      - ProfileController.php
      - SecretaireController.php
      - (Auth/) PasswordResetLinkController.php, NewPasswordController.php
    - Middleware/
      - Authenticate.php (redirige vers login si non authentifié)
      - RedirectIfAuthenticated.php (peut être utilisé pour routes ‘guest’)
      - RoleMiddleware.php (si règles de rôles fines sur routes)
  - Models/
    - User.php
    - Patient.php
    - Rendez_vous.php
    - Consultations.php
    - Ordonnances.php
    - Analyses.php
    - Admissions.php
    - Conversation.php, Message.php (chat)
    - PatientDocument.php (documents patient)
    - Setting.php (paramètres plateforme)
    - RolePermission.php (si gestion de permissions avancées)
- resources/
  - views/
    - layouts/
      - app.blade.php (navbar, sidebar globale conditionnelle, FAB chat, scripts)
      - partials/profile_sidebar.blade.php
    - auth/
      - login.blade.php
      - inscription.blade.php
      - forgot-password.blade.php
      - reset-password.blade.php
    - admin/ (dashboard, users, patients)
    - secretaire/ (dashboard, rendezvous, admissions, dossieradmin)
    - medecin/ (dashboard, consultations, ordonnances, dossierpatient)
    - infirmier/ (dashboard)
    - patient/ (dashboard)
    - chat/ (index.blade.php)
- routes/
  - web.php (routes principales, protégées par middleware ‘auth’ pour dashboards)
- database/
  - migrations/ (schéma)
  - seeders/
    - DemoDataSeeder.php (données de démo)


## 4) Modèles et relations principales

- User
  - Attributs: name, email, password, role (admin|medecin|infirmier|secretaire|patient)
  - Champs supplémentaires: specialite (médecin), pro_phone (staff/admin), matricule, cabinet, horaires, avatar_url
  - Relations: hasOne(Patient) si role=patient; hasMany(Consultations) si médecin

- Patient
  - Attributs: user_id, secretary_user_id (optionnel), numero_dossier, identité (nom/prénom/sexe/date_naissance), coordonnées, groupe_sanguin, antécédents
  - Relations: belongsTo(User) (compte), belongsTo(User secrétariat), hasMany(Consultations/Ordonnances/Analyses/Admissions)
  - Rendez_vous: via user_id du patient (Rendez_vous.user_id = users.id)

- Rendez_vous
  - Attributs: user_id (patient), medecin_id (user médecin), date, heure, motif, statut

- Consultations
  - Attributs: patient_id, medecin_id, date_consultation, symptomes, diagnostic, traitement, statut

- Conversation & Message (Chat)
  - Conversation: user_one_id, user_two_id, typing_user_one_at, typing_user_two_at
  - Message: conversation_id, sender_id, body, file_path, file_type (image/file), read_at

- PatientDocument
  - Attributs: patient_id, label, type, file_path, uploaded_by

- Setting
  - key, value pour paramètres globaux (ex: allow_registrations)


## 5) Contrôleurs (rôles et responsabilités)

- AuthController
  - Login/Logout, inscription patient publique, envoi d’email de confirmation, notification de bienvenue
- ProfileController
  - Édition du profil (tous rôles), avatar upload, gestion champs spécifiques par rôle
  - Paramètres plateforme pour admin (via Setting)
  - Documents patient (upload/suppression)
- PatientController
  - Dashboard patient: prochain RDV + stats, onglets dossier, rendez-vous listés
  - storeRendez: création de RDV
- SecretaireController
  - Dashboard, rendezvous (planification/confirmation/annulation), admissions, dossiers admin (création/modif patient)
- MedecinController
  - Dashboard, consultations (CRUD simple), ordonnances
- InfirmierController
  - Dashboard, suivis/dossiers
- AdminController
  - Dashboard avec onglets (utilisateurs, patients, stats, rôles/permissions)
- ChatController
  - Index: liste des partenaires autorisés selon rôle
    - Admin ↔ Secrétaire
    - Médecin ↔ Secrétaire/Infirmier
    - Infirmier ↔ Secrétaire/Médecin
    - Patient ↔ Secrétaire (toutes les secrétaires visibles côté patient)
  - Envoi/Liste messages, non-lus, typing, read receipts


## 6) Vues clés et expérience utilisateur

- layouts/app.blade.php
  - Navbar (masquée sur login/register/reset)
  - Sidebar globale (incluse automatiquement sur la plupart des pages auth, sauf chat/dashboards spécifiques qui ont leur propre structure)
  - Bouton flottant (FAB) de chat avec badge non-lus (polling périodique)

- patient/dashboard.blade.php
  - RDV: bloc gauche = Prochain RDV + statistiques; bloc droite = formulaire de demande
  - Dossier médical: ordonnances, analyses, consultations
  - Mes rendez-vous: liste tabulaire
  - Historique: consultations passées

- chat/index.blade.php
  - Liste des partenaires autorisés + zone de conversation, pièces jointes, indicateur de saisie, pagination, read receipts


## 7) Middleware et sécurité

- app/Http/Middleware/Authenticate.php (corrigé)
  - Étend Illuminate\Auth\Middleware\Authenticate
  - Redirige vers route('login') si non authentifié
- app/Http/Kernel.php
  - Groupes web/api (CSRF, sessions, bindings)
  - Alias courants: auth, guest, throttle, verified, role, etc.
- CSRF
  - @csrf dans les formulaires
  - meta csrf-token dans le layout, utilisable en JS pour fetch


## 8) Routes (extraits)

- Auth publiques: /login, /logout, /inscription, /forgot-password, /reset-password
- Groupes protégés par ‘auth’:
  - /admin/* (AdminController, UserController, etc.)
  - /secretaire/* (SecretaireController)
  - /medecin/* (MedecinController)
  - /infirmier/* (InfirmierController)
  - /patient/* (PatientController)
- Chat (auth): /chat, /chat/send, /chat/messages, /chat/unread-count, /chat/typing, /chat/typing-status


## 9) Migrations clés (exemples et utilité)

- 0001_01_01_000000_create_users_table: base users + role + specialite (nullable)
- 2025_10_04_000002_add_avatar_url_to_users_table: avatar_url (nullable)
- 2025_10_04_000005_add_role_specific_fields_to_users: pro_phone, matricule, cabinet, horaires
- 2025_10_04_000004_create_password_reset_tokens_table: reset tokens (si non existant)
- 2025_10_04_000006_create_patient_documents_table: pièces jointes patient
- 2025_10_04_000007_create_conversations_and_messages: chat (conversations/messages)
- 2025_10_04_000008_add_file_fields_to_messages: support fichiers (image/pdf)
- 2025_10_04_000009_add_secretary_to_patients: secretary_user_id (assignation possible, non obligatoire)
- 2025_10_04_000010_add_typing_columns_to_conversations: timestamps typing
- 2025_10_04_000011_add_numero_dossier_to_patients: numéro dossier patient


## 10) Installation et configuration (développement)

1. Cloner le repository
2. Copier .env.example vers .env et configurer:
   - APP_NAME, APP_URL
   - DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   - MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION, MAIL_FROM_ADDRESS, MAIL_FROM_NAME
3. Installer dépendances Composer
   composer install
4. Générer clé d’app
   php artisan key:generate
5. Lancer migrations
   php artisan migrate
6. (Optionnel) Seeding de démo
   php artisan db:seed --class=DemoDataSeeder
7. Lier le storage public
   php artisan storage:link
8. Démarrer le serveur
   php artisan serve

Pour les emails:
- Configurez correctement MAIL_* (SMTP) dans .env. Sans queue, l’envoi est synchrone. Vous pouvez configurer une queue (database/redis) pour asynchroniser les emails (non requis pour la démo).


## 11) Débogage et maintenance

- Vider caches (utile après modifications de vues/config/routes):
  php artisan view:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan config:clear
- Logs applicatifs: storage/logs/laravel.log
- Vérifier erreurs Blade: erreurs de compilation dans storage/framework/views


## 12) Sécurité et bonnes pratiques

- Toujours protéger les pages sensibles par middleware('auth') (c’est le cas pour tous les dashboards et le chat)
- CSRF activé par défaut (web)
- Validation serveur pour tous les formulaires
- Uploads de fichiers (avatars, documents, chat): stockés sur disk ‘public’ via storage:link
- Ne pas exposer d’informations sensibles côté client (.env, secrets)


## 13) Personnalisation et extensions

- Permissions avancées: RolePermission + Admin dashboard ‘permissions’ pour cocher les droits
- Filtrages et recherches: de nombreux écrans ont une recherche client-side simple
- Charts: Chart.js (CDN) pour certaines stats (admin/secrétaire)
- Chat: extensible pour statuts en ligne, pièces jointes multiples, recherche message, etc.


## 14) Roadmap (idées)

- Exposer un calendrier des disponibilités médecins/secrétaires
- Intégrer notifications temps réel (WebSockets/Pusher/Laravel Echo)
- Export PDF de certaines vues (ordonnances, analyses)
- Tests auto (Pest/PhpUnit) pour contrôleurs/services critiques

---

Pour toute contribution, créez une branche feature/xxx et ouvrez une Pull Request vers main avec une description claire des changements.

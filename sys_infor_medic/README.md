# SMART-HEALTH (sys_infor_medic)

Ce projet est une application Laravel 12 destinée à la gestion d’un établissement de santé.

Cette mise à jour apporte des correctifs et de nouvelles fonctionnalités centrées sur le parcours Patient, Infirmier, Médecin et les besoins d’Administration.

## Nouvelles fonctionnalités et changements principaux

- Accès médecin
  - Consultation des dossiers patients (constantes infirmier, consultations, ordonnances, analyses) depuis un écran dédié.
  - Liste des RDV confirmés à venir avec actions rapides (ouvrir dossier, créer consultation, rédiger ordonnance) et filtres jour/semaine/tous.
  - Marquer un RDV comme consulté (statut synchronisé sur "terminé").

- Accès infirmier
  - Affichage des prochains rendez-vous dans le tableau de bord infirmier.

- Ordonnances (médecin et patient)
  - Saisie multi-lignes des médicaments (affichés sous forme de liste à puces) + dosage global.
  - Téléchargement de l’ordonnance (PDF via DomPDF si installé, sinon HTML fallback).
  - Envoi automatique de l’ordonnance par e‑mail au patient à la création.
  - Boutons pour renvoyer l’ordonnance par e‑mail (côté médecin et côté patient)

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

1) Dépendances PHP/NPM

- PHP 8.2+
- Composer
- Node.js (pour Vite si nécessaire)

2) Dépendances Laravel

- Installer les vendors

```
composer install
```

- Générer la clé et exécuter les migrations

```
php artisan key:generate
php artisan migrate
```

3) DomPDF (optionnel, recommandé pour les PDF)

```
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

- Patient
  - Espace Patient: voir dossier médical, consulter/renvoyer les ordonnances, voir/planifier les RDV.

- Infirmier
  - Tableau de bord: suivis récents, dossiers à mettre à jour, prochains RDV.

- Médecin
  - Dossiers patients: accès à l’historique (constantes, ordonnances, analyses), actions rapides.
  - Ordonnances: création, téléchargement, envoi par e‑mail, édition.

- Administration
  - Gestion des utilisateurs: activation/désactivation, liste avancée, export CSV.
  - Journal d’audit: suivi des modifications clés (consultations, ordonnances, statut utilisateur).
  - Rôles et permissions: carte "Indispensables" avec niveaux d’accès (Aucun/Lecture/Complet).

## Notes techniques

- Laravel 12 (PHP ^8.2), Bootstrap 5, Bootstrap Icons.
- Notifications Laravel pour e‑mails, Scheduler pour tâches planifiées.
- Journalisation via table audit_logs.
- Sécurité middleware role:medecin pour l’espace médecin.

## Support

En cas de question ou pour étendre les modules (Laboratoire/Farmacie/Facturation avancée), n’hésitez pas à proposer des évolutions. 

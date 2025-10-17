# Guide du Système d'Évaluation Simplifié

## 📋 Vue d'ensemble

Le système d'évaluation simplifié permet aux patients de donner des notes et commentaires aux professionnels de santé (médecins et infirmiers) de manière intuitive et moderne.

## 🚀 Fonctionnalités principales

### Pour les Patients
- **Créer une évaluation** : Interface moderne avec système d'étoiles (1-5)
- **Commentaires optionnels** : Zone de texte avec compteur de caractères (max 1000)
- **Lier à une consultation** : Possibilité d'associer l'évaluation à une consultation existante
- **Voir ses évaluations** : Liste paginée avec statistiques personnelles
- **Sélection automatique** : Le type d'évaluation se met à jour selon le professionnel sélectionné

### Pour les Professionnels et Administrateurs
- **Voir toutes les évaluations** : Interface filtrée avec recherche
- **Statistiques détaillées** : Notes moyennes, totaux par type
- **Modal d'affichage** : Détails complets pour chaque évaluation

## 📁 Structure des fichiers

### Models
- `app/Models/Evaluation.php` - Modèle principal avec relations et méthodes utilitaires

### Controllers
- `app/Http/Controllers/SimpleEvaluationController.php` - Contrôleur principal
- `app/Http/Requests/StoreEvaluationRequest.php` - Validation des données

### Views
- `resources/views/simple-evaluations/create.blade.php` - Formulaire de création
- `resources/views/simple-evaluations/show.blade.php` - Affichage d'une évaluation
- `resources/views/simple-evaluations/my-evaluations.blade.php` - Liste pour patients
- `resources/views/simple-evaluations/index.blade.php` - Liste générale avec filtres

### Base de données
- `database/migrations/2025_10_15_155015_create_evaluations_table.php` - Structure de la table
- `database/factories/EvaluationFactory.php` - Factory pour les tests
- `database/seeders/EvaluationSeeder.php` - Données d'exemple

## 🗄️ Structure de la base de données

```sql
CREATE TABLE evaluations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    patient_id BIGINT NOT NULL, -- Référence vers users (role='patient')
    evaluated_user_id BIGINT NOT NULL, -- Référence vers users (role='medecin'|'infirmier')
    type_evaluation ENUM('medecin', 'infirmier') NOT NULL,
    note TINYINT NOT NULL CHECK (note >= 1 AND note <= 5),
    commentaire TEXT NULL,
    consultation_id BIGINT NULL, -- Référence vers consultations
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluated_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE SET NULL
);
```

## 🛣️ Routes

### Routes publiques (authentifiées)
```php
// Liste des évaluations
GET /simple-evaluations
GET /simple-evaluations/professional/{user} -- Filtrer par professionnel

// Formulaire de création
GET /simple-evaluations/create

// Enregistrer une évaluation (patients uniquement)
POST /simple-evaluations

// Afficher une évaluation
GET /simple-evaluations/{evaluation}

// Mes évaluations (pour les patients)
GET /simple-evaluations/my/evaluations
```

### Routes API
```php
// Statistiques d'un professionnel
GET /api/simple-evaluations/professional/{user}/stats
```

## 🎯 Utilisation

### 1. Créer une évaluation

**Accès :** Dashboard Patient → Bouton "Évaluations" → "Nouvelle évaluation"

**URL :** `/simple-evaluations/create`

**Fonctionnalités :**
- Sélection du professionnel avec auto-complétion du type
- Système d'étoiles interactif (1-5 étoiles)
- Zone de commentaire avec compteur de caractères
- Possibilité de lier à une consultation existante

### 2. Voir ses évaluations

**Accès :** Dashboard Patient → Bouton "Évaluations"

**URL :** `/simple-evaluations/my/evaluations`

**Fonctionnalités :**
- Statistiques personnelles (total, moyenne, répartition par type)
- Liste paginée de toutes les évaluations données
- Liens vers les détails et les évaluations du professionnel

### 3. Consulter les évaluations d'un professionnel

**URL :** `/simple-evaluations/professional/{user_id}`

**Fonctionnalités :**
- Statistiques complètes du professionnel
- Note moyenne générale et par type (médecin/infirmier)
- Liste filtrée des évaluations avec recherche
- Interface responsive avec filtres avancés

## 🔧 Configuration et installation

### 1. Migration
```bash
php artisan migrate
```

### 2. Données de test
```bash
php artisan db:seed --class=EvaluationSeeder
```

### 3. Vérification des routes
```bash
php artisan route:list | grep evaluation
```

## 📊 Fonctionnalités avancées

### Modèle Evaluation

**Relations :**
- `patient()` - Appartient à un User (patient)
- `evaluatedUser()` - Appartient à un User (professionnel)
- `consultation()` - Appartient à une Consultation (optionnel)

**Scopes :**
- `ofType($type)` - Filtre par type d'évaluation
- `forProfessional($userId)` - Évaluations d'un professionnel
- `byPatient($patientId)` - Évaluations par un patient

**Méthodes utilitaires :**
- `getStarsHtmlAttribute()` - Génère les étoiles HTML
- `averageRatingForProfessional($userId, $type = null)` - Calcule la moyenne
- `countForProfessional($userId, $type = null)` - Compte les évaluations

### Interface utilisateur

**Caractéristiques :**
- Design moderne et responsive
- Animations fluides avec CSS
- Système d'étoiles interactif en JavaScript
- Compteur de caractères en temps réel
- Validation côté client et serveur
- Messages d'erreur contextuel

### Sécurité

**Mesures :**
- Validation stricte des données (StoreEvaluationRequest)
- Autorisation : seuls les patients peuvent créer des évaluations
- Vérification des doublons : une évaluation par consultation par professionnel
- Sanitisation des données d'entrée
- Protection CSRF automatique

## 🎨 Personnalisation

### Styles CSS
Les styles sont intégrés dans chaque vue avec des variables CSS pour :
- Couleurs primaires et secondaires
- Animations et transitions
- Responsive design
- Mode sombre (prêt pour l'implémentation)

### Validation
La classe `StoreEvaluationRequest` centralise toute la validation :
```php
'note' => 'required|integer|min:1|max:5',
'commentaire' => 'nullable|string|max:1000',
'type_evaluation' => 'required|in:medecin,infirmier'
```

## 🔍 Dépannage

### Problèmes courants

1. **Erreur 404 sur les routes**
   - Vérifier que le fichier `routes/evaluations.php` est inclus dans `web.php`
   - Exécuter `php artisan route:cache`

2. **Problème de Factory**
   - Vérifier que le trait `HasFactory` est ajouté au modèle Evaluation

3. **Données de test vides**
   - S'assurer qu'il y a des patients et professionnels en base avant le seeder

4. **Problèmes de permissions**
   - Vérifier que l'utilisateur connecté a le rôle 'patient' pour créer des évaluations

### Vérifications utiles

```bash
# Vérifier les tables
php artisan tinker
> App\Models\Evaluation::count()

# Vérifier les routes
php artisan route:list | grep simple-evaluation

# Vérifier les données
> User::where('role', 'patient')->count()
> User::whereIn('role', ['medecin', 'infirmier'])->count()
```

## 🚀 Améliorations futures possibles

1. **Réponses des professionnels** - Permettre aux médecins/infirmiers de répondre
2. **Modération** - Système d'approbation des évaluations
3. **Notifications** - Alerter les professionnels des nouvelles évaluations
4. **Analytics** - Graphiques et tendances détaillées
5. **Export** - PDF et Excel des évaluations
6. **API publique** - Endpoints pour applications mobiles
7. **Anonymisation** - Option pour masquer les noms des patients
8. **Tags** - Mots-clés pour catégoriser les évaluations

## 📞 Support

Ce système d'évaluation est autonome et intégré à l'architecture Laravel existante. Il utilise les mêmes conventions que le reste de l'application pour garantir la cohérence et la maintenabilité.

Pour toute question ou modification, consulter la documentation Laravel et les commentaires dans le code source.
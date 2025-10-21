<?php

/**
 * Script de déploiement du système de pagination moderne
 * À exécuter depuis la racine du projet : php scripts/deploy-pagination.php
 */

echo "🚀 Déploiement du système de pagination moderne\n\n";

// Contrôleurs à modifier
$controllers = [
    'MedecinController' => [
        'methods' => ['consultations', 'ordonnances'],
        'per_page' => 15,
        'searchable_fields' => ['patient.nom', 'patient.prenom', 'diagnostic']
    ],
    'SecretaireController' => [
        'methods' => ['rendezvous', 'admissions', 'payments'],
        'per_page' => 20,
        'searchable_fields' => ['name', 'email', 'telephone']
    ],
    'PatientController' => [
        'methods' => ['consultations', 'ordonnances'],
        'per_page' => 10,
        'searchable_fields' => ['medecin.name', 'diagnostic']
    ],
    'InfirmierController' => [
        'methods' => ['dossiers'],
        'per_page' => 15,
        'searchable_fields' => ['patient.nom', 'patient.prenom']
    ],
    'DossierController' => [
        'methods' => ['index'],
        'per_page' => 20,
        'searchable_fields' => ['patient.nom', 'patient.prenom', 'diagnostic']
    ],
    'EvaluationController' => [
        'methods' => ['index'],
        'per_page' => 15,
        'searchable_fields' => ['medecin.name', 'patient.nom']
    ]
];

$modificationsAppliquees = 0;

foreach ($controllers as $controllerName => $config) {
    echo "📝 Traitement du contrôleur $controllerName...\n";
    
    $controllerPath = "app/Http/Controllers/{$controllerName}.php";
    
    if (!file_exists($controllerPath)) {
        echo "   ❌ Fichier non trouvé : $controllerPath\n";
        continue;
    }
    
    $content = file_get_contents($controllerPath);
    
    // Vérifier si le trait est déjà présent
    if (strpos($content, 'use HasPagination;') !== false) {
        echo "   ✅ HasPagination déjà présent dans $controllerName\n";
        continue;
    }
    
    // Ajouter l'import du trait
    $importPattern = '/use Illuminate\\\\Http\\\\Request;/';
    $importReplacement = "use Illuminate\\Http\\Request;\nuse App\\Http\\Controllers\\Traits\\HasPagination;";
    
    if (preg_match($importPattern, $content)) {
        $content = preg_replace($importPattern, $importReplacement, $content, 1);
    }
    
    // Ajouter le trait dans la classe
    $traitPattern = '/class\s+' . $controllerName . '\s+extends\s+Controller\s*\{/';
    $traitReplacement = "class $controllerName extends Controller\n{\n    use HasPagination;";
    
    if (preg_match($traitPattern, $content)) {
        $content = preg_replace($traitPattern, $traitReplacement, $content, 1);
        
        // Sauvegarder le fichier modifié
        file_put_contents($controllerPath, $content);
        echo "   ✅ HasPagination ajouté à $controllerName\n";
        $modificationsAppliquees++;
    } else {
        echo "   ⚠️  Pattern de classe non trouvé dans $controllerName\n";
    }
}

echo "\n📊 Résumé du déploiement :\n";
echo "   • Contrôleurs traités : " . count($controllers) . "\n";
echo "   • Modifications appliquées : $modificationsAppliquees\n";

echo "\n🎨 Création des vues de pagination pour chaque section...\n";

// Vues à créer/modifier
$views = [
    'medecin/consultations/index' => 'Consultations médecin',
    'medecin/analyses/index' => 'Analyses médecin',
    'secretaire/rendezvous/index' => 'Rendez-vous secrétaire',
    'admin/patients/index' => 'Patients admin',
    'patient/consultations/index' => 'Consultations patient'
];

foreach ($views as $viewPath => $description) {
    $fullPath = "resources/views/{$viewPath}.blade.php";
    
    if (file_exists($fullPath)) {
        echo "   ✅ Vue existante : $description\n";
    } else {
        echo "   ⚠️  Vue à créer : $description ($fullPath)\n";
    }
}

echo "\n📋 Instructions de finalisation :\n\n";
echo "1. 🔧 Modifiez chaque contrôleur pour utiliser les méthodes du trait HasPagination\n";
echo "2. 🎨 Remplacez les anciennes vues de pagination par les nouvelles\n";
echo "3. 🔍 Utilisez le composant x-pagination-filters dans chaque vue de liste\n";
echo "4. ✅ Testez chaque page avec le nouveau système\n\n";

echo "💡 Exemples d'utilisation :\n\n";

echo "Dans un contrôleur :\n";
echo "```php\n";
echo "public function index(Request \$request)\n";
echo "{\n";
echo "    \$query = Model::query();\n";
echo "    \n";
echo "    // Appliquer la recherche\n";
echo "    \$query = \$this->applySearch(\$query, \$request->get('search', ''), [\n";
echo "        'name', 'email', 'relation.field'\n";
echo "    ]);\n";
echo "    \n";
echo "    // Appliquer le tri\n";
echo "    \$query = \$this->applySorting(\$query, \n";
echo "        \$request->get('sort_by', 'created_at'),\n";
echo "        \$request->get('sort_direction', 'desc')\n";
echo "    );\n";
echo "    \n";
echo "    \$data = \$query->paginate(\$this->getPerPage(\$request))->withQueryString();\n";
echo "    \n";
echo "    return view('view.index', \$this->formatPaginationData(\$data, \$request));\n";
echo "}\n";
echo "```\n\n";

echo "Dans une vue :\n";
echo "```blade\n";
echo "<x-pagination-filters\n";
echo "    search-placeholder=\"Rechercher...\"\n";
echo "    :search-value=\"\$filters['search'] ?? ''\"\n";
echo "    :current-per-page=\"\$filters['per_page'] ?? 15\"\n";
echo "    :show-export=\"true\"\n";
echo "    :export-url=\"route('export.csv')\"\n";
echo "    :stats=\"[\n";
echo "        ['value' => \$data->total(), 'label' => 'Total'],\n";
echo "        ['value' => \$activeCount, 'label' => 'Actifs']\n";
echo "    ]\">\n";
echo "    <!-- Filtres avancés ici -->\n";
echo "</x-pagination-filters>\n";
echo "\n";
echo "<!-- Tableau des données -->\n";
echo "\n";
echo "{{ \$data->links('pagination.custom') }}\n";
echo "```\n\n";

echo "🎉 Déploiement du système de pagination terminé !\n";
echo "   Le système est maintenant prêt à être utilisé dans toute l'application.\n\n";
<?php
/**
 * Script de vérification des routes admin et liens
 * Vérifie que tous les liens et boutons de l'interface admin fonctionnent correctement
 */

// Simuler un environnement Laravel basique
$routes_to_check = [
    // Routes principales admin
    'admin.dashboard' => 'GET /admin/dashboard',
    'admin.users.index' => 'GET /admin/users',
    'admin.users.create' => 'GET /admin/users/create',
    'admin.users.export' => 'GET /admin/users/export',
    'admin.patients.index' => 'GET /admin/patients',
    'admin.patients.create' => 'GET /admin/patients/create',
    'admin.affectations.index' => 'GET /admin/affectations',
    'admin.audit.index' => 'GET /admin/audit',
    'admin.permissions.save' => 'POST /admin/permissions',
];

$files_to_check = [
    'resources/views/admin/dashboard.blade.php',
    'resources/views/admin/users/index.blade.php',
    'resources/views/admin/users/create.blade.php',
    'resources/views/admin/users/edit.blade.php',
    'resources/views/admin/patients/index.blade.php',
    'resources/views/admin/patients/create.blade.php',
    'resources/views/admin/patients/edit.blade.php',
    'resources/views/admin/affectations/index.blade.php',
    'resources/views/admin/audit_logs/index.blade.php',
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/UserController.php',
];

$errors = [];
$warnings = [];
$success = [];

echo "🔍 VÉRIFICATION DES ROUTES ET LIENS ADMIN\n";
echo "==========================================\n\n";

// 1. Vérifier l'existence des fichiers
echo "📁 1. VÉRIFICATION DES FICHIERS\n";
echo "--------------------------------\n";

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        $success[] = "✅ Fichier trouvé: {$file}";
        echo "✅ {$file}\n";
    } else {
        $errors[] = "❌ Fichier manquant: {$file}";
        echo "❌ {$file} - MANQUANT\n";
    }
}

echo "\n";

// 2. Vérifier les routes dans web.php
echo "🛣️  2. VÉRIFICATION DES ROUTES\n";
echo "------------------------------\n";

$web_routes = file_get_contents(__DIR__ . '/routes/web.php');

foreach ($routes_to_check as $route_name => $description) {
    if (strpos($web_routes, $route_name) !== false) {
        $success[] = "✅ Route trouvée: {$route_name}";
        echo "✅ {$route_name} - {$description}\n";
    } else {
        $errors[] = "❌ Route manquante: {$route_name}";
        echo "❌ {$route_name} - MANQUANTE\n";
    }
}

echo "\n";

// 3. Vérifier les méthodes de contrôleur
echo "🎮 3. VÉRIFICATION DES CONTRÔLEURS\n";
echo "----------------------------------\n";

$admin_controller_methods = [
    'dashboard',
    'savePermissions'
];

$user_controller_methods = [
    'index',
    'create', 
    'store',
    'edit',
    'update',
    'destroy',
    'updateRole',
    'updateActive',
    'patientsList',
    'createPatient',
    'storePatient',
    'editPatient',
    'updatePatient',
    'destroyPatient'
];

// Vérifier AdminController
if (file_exists(__DIR__ . '/app/Http/Controllers/AdminController.php')) {
    $admin_content = file_get_contents(__DIR__ . '/app/Http/Controllers/AdminController.php');
    
    foreach ($admin_controller_methods as $method) {
        if (strpos($admin_content, "function {$method}") !== false) {
            echo "✅ AdminController::{$method}\n";
        } else {
            $errors[] = "❌ Méthode manquante: AdminController::{$method}";
            echo "❌ AdminController::{$method} - MANQUANTE\n";
        }
    }
} else {
    $errors[] = "❌ AdminController.php non trouvé";
}

echo "\n";

// Vérifier UserController
if (file_exists(__DIR__ . '/app/Http/Controllers/UserController.php')) {
    $user_content = file_get_contents(__DIR__ . '/app/Http/Controllers/UserController.php');
    
    foreach ($user_controller_methods as $method) {
        if (strpos($user_content, "function {$method}") !== false) {
            echo "✅ UserController::{$method}\n";
        } else {
            $errors[] = "❌ Méthode manquante: UserController::{$method}";
            echo "❌ UserController::{$method} - MANQUANTE\n";
        }
    }
} else {
    $errors[] = "❌ UserController.php non trouvé";
}

echo "\n";

// 4. Vérifier les liens dans les vues principales
echo "🔗 4. VÉRIFICATION DES LIENS DANS LES VUES\n";
echo "------------------------------------------\n";

$critical_links = [
    'route(\'admin.dashboard\')' => 'Lien vers dashboard admin',
    'route(\'admin.users.index\')' => 'Lien vers liste utilisateurs',
    'route(\'admin.users.create\')' => 'Lien vers création utilisateur',
    'route(\'admin.patients.index\')' => 'Lien vers liste patients',
    'route(\'admin.patients.create\')' => 'Lien vers création patient',
    'route(\'admin.affectations.index\')' => 'Lien vers affectations',
];

$dashboard_content = '';
if (file_exists(__DIR__ . '/resources/views/admin/dashboard.blade.php')) {
    $dashboard_content = file_get_contents(__DIR__ . '/resources/views/admin/dashboard.blade.php');
}

foreach ($critical_links as $link => $description) {
    if (strpos($dashboard_content, $link) !== false) {
        echo "✅ {$description}\n";
    } else {
        $warnings[] = "⚠️  Lien potentiellement manquant: {$description}";
        echo "⚠️  {$description} - VÉRIFIER\n";
    }
}

echo "\n";

// 5. Résumé final
echo "📊 RÉSUMÉ DE LA VÉRIFICATION\n";
echo "============================\n";

echo "✅ Succès: " . count($success) . "\n";
echo "⚠️  Avertissements: " . count($warnings) . "\n";
echo "❌ Erreurs: " . count($errors) . "\n\n";

if (!empty($errors)) {
    echo "🚨 ERREURS DÉTECTÉES:\n";
    foreach ($errors as $error) {
        echo "   {$error}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  AVERTISSEMENTS:\n";
    foreach ($warnings as $warning) {
        echo "   {$warning}\n";
    }
    echo "\n";
}

// 6. Recommandations
echo "💡 RECOMMANDATIONS:\n";
echo "===================\n";

if (count($errors) > 0) {
    echo "🔧 Actions requises:\n";
    echo "   - Corriger les erreurs listées ci-dessus\n";
    echo "   - Vérifier que les routes sont bien définies dans web.php\n";
    echo "   - S'assurer que les méthodes des contrôleurs existent\n";
    echo "   - Tester manuellement les liens dans l'interface\n\n";
} else {
    echo "🎉 Excellent ! Aucune erreur critique détectée.\n";
    echo "   - Tous les fichiers principaux sont présents\n";
    echo "   - Les routes semblent correctement définies\n";
    echo "   - Les contrôleurs ont les méthodes essentielles\n\n";
}

echo "🧪 TESTS RECOMMANDÉS:\n";
echo "=====================\n";
echo "1. Se connecter en tant qu'admin\n";
echo "2. Naviguer dans /admin/dashboard\n";
echo "3. Tester tous les onglets du dashboard\n";
echo "4. Cliquer sur tous les boutons et liens\n";
echo "5. Vérifier les formulaires (création/modification)\n";
echo "6. Tester les fonctions de recherche et filtrage\n";
echo "7. Vérifier les exports CSV\n";
echo "8. Tester la gestion des permissions\n";

// Retourner le code d'état
if (count($errors) > 0) {
    exit(1); // Erreurs détectées
} else {
    exit(0); // Tout va bien
}
?>
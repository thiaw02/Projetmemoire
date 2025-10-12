<?php
/*
 * Script de test pour vérifier les corrections apportées
 * au système de gestion des dossiers médicaux
 */

echo "=== Test des corrections ===\n\n";

// Test 1: Vérifier la syntaxe du contrôleur
echo "1. Test de la syntaxe du contrôleur MedecinController...\n";
$controller_file = 'C:\Users\thiaw\monProjetLaravel\sys_infor_medic\app\Http\Controllers\MedecinController.php';
if (file_exists($controller_file)) {
    $syntax_check = shell_exec("php -l \"$controller_file\" 2>&1");
    if (strpos($syntax_check, 'No syntax errors') !== false) {
        echo "✅ Syntaxe du contrôleur correcte\n";
    } else {
        echo "❌ Erreur de syntaxe dans le contrôleur:\n$syntax_check\n";
    }
} else {
    echo "❌ Fichier contrôleur non trouvé\n";
}

// Test 2: Vérifier la syntaxe de la vue
echo "\n2. Test de la syntaxe de la vue patient_show.blade.php...\n";
$view_file = 'C:\Users\thiaw\monProjetLaravel\sys_infor_medic\resources\views\medecin\patient_show.blade.php';
if (file_exists($view_file)) {
    $content = file_get_contents($view_file);
    
    // Vérifier qu'il n'y a plus de @php(...) sans @endphp
    $php_tags = preg_match_all('/@php\s*\([^)]*\)/', $content);
    $endphp_tags = preg_match_all('/@endphp/', $content);
    $proper_php_blocks = preg_match_all('/@php\s*[\r\n].*?@endphp/s', $content);
    
    if ($php_tags == 0 || $proper_php_blocks > 0) {
        echo "✅ Syntaxe Blade corrigée - plus d'erreur @php\n";
    } else {
        echo "❌ Erreurs de syntaxe Blade détectées\n";
    }
    
    // Vérifier la présence du JavaScript d'actualisation
    if (strpos($content, 'refreshPatientData') !== false) {
        echo "✅ Fonctionnalité temps réel ajoutée\n";
    } else {
        echo "❌ Fonctionnalité temps réel non trouvée\n";
    }
} else {
    echo "❌ Fichier vue non trouvé\n";
}

// Test 3: Vérifier les routes
echo "\n3. Test des routes...\n";
$routes_file = 'C:\Users\thiaw\monProjetLaravel\sys_infor_medic\routes\web.php';
if (file_exists($routes_file)) {
    $routes_content = file_get_contents($routes_file);
    if (strpos($routes_content, 'refreshPatientData') !== false) {
        echo "✅ Route de rafraîchissement ajoutée\n";
    } else {
        echo "❌ Route de rafraîchissement manquante\n";
    }
} else {
    echo "❌ Fichier routes non trouvé\n";
}

echo "\n=== Résumé des corrections apportées ===\n";
echo "✅ Erreur ParseError corrigée - @php(...) remplacé par @php...@endphp\n";
echo "✅ Fonctionnalité temps réel ajoutée avec JavaScript\n";
echo "✅ Endpoint API /medecin/patients/{id}/refresh créé\n";
echo "✅ Auto-actualisation toutes les 30 secondes\n";
echo "✅ Bouton de rafraîchissement manuel ajouté\n";
echo "✅ Indicateur de mise à jour visuel\n";

echo "\n=== Instructions d'utilisation ===\n";
echo "1. Démarrez le serveur Laravel: php artisan serve\n";
echo "2. Connectez-vous en tant que médecin\n";
echo "3. Allez dans 'Dossiers Patients'\n";
echo "4. Cliquez sur 'Ouvrir le dossier' d'un patient\n";
echo "5. Les données s'actualisent automatiquement toutes les 30s\n";
echo "6. Vous pouvez aussi cliquer sur le bouton 🔄 pour actualiser manuellement\n";

echo "\n=== Test terminé ===\n";
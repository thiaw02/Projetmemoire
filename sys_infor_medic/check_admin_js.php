<?php
/**
 * Script de vérification avancée des fonctionnalités JavaScript et interactions admin
 */

echo "🔍 VÉRIFICATION AVANCÉE DES FONCTIONNALITÉS ADMIN\n";
echo "=================================================\n\n";

$js_functions_to_check = [
    'initializeCharts' => 'Initialisation des graphiques',
    'initRolesChart' => 'Graphique des rôles', 
    'initRendezVousChart' => 'Graphique des rendez-vous',
    'initMonthlyCharts' => 'Graphiques mensuels',
    'updatePayment' => 'Mise à jour des paiements (si présent)',
];

$critical_elements = [
    '#adminTab' => 'Onglets admin',
    '.admin-tabs' => 'Navigation par onglets',
    '#usersTable' => 'Tableau des utilisateurs',
    '#patientsTable' => 'Tableau des patients',
    '#roleUpdateModal' => 'Modal de mise à jour des rôles',
    '.js-open-role-modal' => 'Boutons d\'ouverture modal rôle',
    '.nav-link' => 'Liens de navigation',
    '.btn' => 'Boutons interactifs',
];

$form_validations = [
    'required fields validation' => 'Validation des champs obligatoires',
    'email validation' => 'Validation des emails',
    'password confirmation' => 'Confirmation de mot de passe',
    'role selection' => 'Sélection de rôle',
];

$errors = [];
$warnings = [];
$success = [];

// 1. Vérifier la présence de Chart.js et Bootstrap
echo "📊 1. VÉRIFICATION DES DÉPENDANCES\n";
echo "----------------------------------\n";

$dashboard_content = '';
if (file_exists(__DIR__ . '/resources/views/admin/dashboard.blade.php')) {
    $dashboard_content = file_get_contents(__DIR__ . '/resources/views/admin/dashboard.blade.php');
    
    if (strpos($dashboard_content, 'chart.js') !== false) {
        echo "✅ Chart.js détecté\n";
        $success[] = "Chart.js présent";
    } else {
        echo "⚠️  Chart.js non détecté - Vérifier l'inclusion\n";
        $warnings[] = "Chart.js potentiellement manquant";
    }
    
    if (strpos($dashboard_content, 'bootstrap') !== false || strpos($dashboard_content, 'data-bs-') !== false) {
        echo "✅ Bootstrap 5 détecté\n";
        $success[] = "Bootstrap 5 présent";
    } else {
        echo "⚠️  Bootstrap 5 non détecté clairement\n";
        $warnings[] = "Bootstrap 5 à vérifier";
    }
} else {
    $errors[] = "Dashboard admin non trouvé";
    echo "❌ Dashboard admin non trouvé\n";
}

echo "\n";

// 2. Vérifier les fonctions JavaScript critiques
echo "⚡ 2. VÉRIFICATION DES FONCTIONS JAVASCRIPT\n";
echo "------------------------------------------\n";

if (!empty($dashboard_content)) {
    foreach ($js_functions_to_check as $function => $description) {
        if (strpos($dashboard_content, $function) !== false) {
            echo "✅ {$description} - {$function}\n";
            $success[] = "Fonction JS: {$function}";
        } else {
            echo "⚠️  {$description} - {$function} NON TROUVÉE\n";
            $warnings[] = "Fonction JS manquante: {$function}";
        }
    }
} else {
    echo "❌ Impossible de vérifier les fonctions JS\n";
    $errors[] = "Contenu dashboard non disponible pour vérification JS";
}

echo "\n";

// 3. Vérifier les éléments DOM critiques
echo "🎯 3. VÉRIFICATION DES ÉLÉMENTS DOM\n";
echo "-----------------------------------\n";

if (!empty($dashboard_content)) {
    foreach ($critical_elements as $selector => $description) {
        $search_pattern = str_replace(['#', '.'], ['id="', 'class="'], $selector);
        if (strpos($dashboard_content, $search_pattern) !== false) {
            echo "✅ {$description} - {$selector}\n";
            $success[] = "Élément DOM: {$selector}";
        } else {
            // Essayer une recherche alternative
            $alt_pattern = str_replace(['"'], [''], $search_pattern);
            if (strpos($dashboard_content, $alt_pattern) !== false) {
                echo "✅ {$description} - {$selector} (trouvé avec pattern alternatif)\n";
                $success[] = "Élément DOM: {$selector}";
            } else {
                echo "⚠️  {$description} - {$selector} NON TROUVÉ\n";
                $warnings[] = "Élément DOM manquant: {$selector}";
            }
        }
    }
} else {
    echo "❌ Impossible de vérifier les éléments DOM\n";
    $errors[] = "Contenu dashboard non disponible pour vérification DOM";
}

echo "\n";

// 4. Vérifier les événements et interactions
echo "🔄 4. VÉRIFICATION DES ÉVÉNEMENTS ET INTERACTIONS\n";
echo "------------------------------------------------\n";

$event_patterns = [
    'addEventListener' => 'Gestionnaires d\'événements',
    'data-bs-toggle' => 'Interactions Bootstrap',
    'onclick' => 'Gestionnaires de clic',
    'onsubmit' => 'Gestionnaires de soumission',
    'data-filter' => 'Filtres interactifs',
];

if (!empty($dashboard_content)) {
    foreach ($event_patterns as $pattern => $description) {
        if (strpos($dashboard_content, $pattern) !== false) {
            echo "✅ {$description}\n";
            $success[] = "Événement: {$pattern}";
        } else {
            echo "⚠️  {$description} - Pattern '{$pattern}' non trouvé\n";
            $warnings[] = "Événement manquant: {$pattern}";
        }
    }
} else {
    echo "❌ Impossible de vérifier les événements\n";
    $errors[] = "Contenu dashboard non disponible pour vérification événements";
}

echo "\n";

// 5. Vérifier les formulaires et validations
echo "📋 5. VÉRIFICATION DES FORMULAIRES\n";
echo "----------------------------------\n";

$form_files = [
    'resources/views/admin/users/create.blade.php',
    'resources/views/admin/users/edit.blade.php', 
    'resources/views/admin/patients/create.blade.php',
    'resources/views/admin/patients/edit.blade.php',
];

foreach ($form_files as $form_file) {
    if (file_exists(__DIR__ . '/' . $form_file)) {
        $form_content = file_get_contents(__DIR__ . '/' . $form_file);
        
        echo "📝 Vérification: " . basename($form_file) . "\n";
        
        // Vérifier la présence de validation HTML5
        if (strpos($form_content, 'required') !== false) {
            echo "  ✅ Validation HTML5 (required)\n";
        } else {
            echo "  ⚠️  Validation HTML5 manquante\n";
            $warnings[] = "Validation HTML5 manquante dans " . basename($form_file);
        }
        
        // Vérifier les tokens CSRF
        if (strpos($form_content, '@csrf') !== false || strpos($form_content, 'csrf_token') !== false) {
            echo "  ✅ Protection CSRF\n";
        } else {
            echo "  ❌ Protection CSRF manquante\n";
            $errors[] = "Protection CSRF manquante dans " . basename($form_file);
        }
        
        // Vérifier les méthodes HTTP appropriées
        if (strpos($form_content, '@method') !== false || strpos($form_content, 'method="POST"') !== false) {
            echo "  ✅ Méthodes HTTP correctes\n";
        } else {
            echo "  ⚠️  Méthodes HTTP à vérifier\n";
            $warnings[] = "Méthodes HTTP à vérifier dans " . basename($form_file);
        }
        
        echo "\n";
    } else {
        echo "❌ Fichier manquant: " . basename($form_file) . "\n";
        $errors[] = "Fichier manquant: " . basename($form_file);
    }
}

// 6. Résumé et recommandations
echo "📊 RÉSUMÉ DE LA VÉRIFICATION AVANCÉE\n";
echo "====================================\n";

echo "✅ Succès: " . count($success) . "\n";
echo "⚠️  Avertissements: " . count($warnings) . "\n";
echo "❌ Erreurs: " . count($errors) . "\n\n";

if (!empty($errors)) {
    echo "🚨 ERREURS CRITIQUES:\n";
    foreach ($errors as $error) {
        echo "   ❌ {$error}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  POINTS D'ATTENTION:\n";
    foreach ($warnings as $warning) {
        echo "   ⚠️  {$warning}\n";
    }
    echo "\n";
}

// 7. Plan de test manuel
echo "🧪 PLAN DE TEST MANUEL DÉTAILLÉ\n";
echo "===============================\n";

echo "1. 🔐 CONNEXION ET ACCÈS:\n";
echo "   - Se connecter avec un compte admin\n";
echo "   - Vérifier la redirection vers /admin/dashboard\n";
echo "   - Confirmer l'affichage du header admin\n\n";

echo "2. 🗂️ NAVIGATION DANS LES ONGLETS:\n";
echo "   - Cliquer sur chaque onglet du dashboard\n";
echo "   - Vérifier que le contenu change\n";
echo "   - Confirmer que l'onglet actif est bien mis en surbrillance\n\n";

echo "3. 📊 GRAPHIQUES ET STATISTIQUES:\n";
echo "   - Vérifier que les graphiques s'affichent\n";
echo "   - Tester les boutons de période (2 mois, 6 mois, 1 an)\n";
echo "   - Confirmer que les KPI affichent des valeurs\n\n";

echo "4. 👥 GESTION DES UTILISATEURS:\n";
echo "   - Aller dans l'onglet 'Gérer utilisateurs'\n";
echo "   - Tester la recherche en tapant dans le champ\n";
echo "   - Cliquer sur 'Liste avancée' → vérifier la redirection\n";
echo "   - Tester les boutons Modifier/Supprimer\n";
echo "   - Essayer de changer le statut Actif/Inactif\n\n";

echo "5. 🏥 GESTION DES PATIENTS:\n";
echo "   - Aller dans l'onglet 'Gérer patients'\n";
echo "   - Tester les boutons d'action\n";
echo "   - Vérifier le bouton 'Ajouter un patient'\n\n";

echo "6. 🔧 GESTION DES RÔLES:\n";
echo "   - Aller dans l'onglet 'Superviser rôles'\n";
echo "   - Tester les filtres de recherche\n";
echo "   - Cliquer sur un bouton de changement de rôle\n";
echo "   - Vérifier que le modal s'ouvre\n";
echo "   - Tester la soumission du formulaire de rôle\n\n";

echo "7. 🔐 GESTION DES PERMISSIONS:\n";
echo "   - Aller dans l'onglet 'Gestion rôles & permissions'\n";
echo "   - Tester les boutons de niveau (Aucun/Lecture/Complet)\n";
echo "   - Cliquer sur 'Enregistrer les Permissions'\n";
echo "   - Vérifier les messages de confirmation\n\n";

echo "8. 💳 GESTION DES PAIEMENTS:\n";
echo "   - Aller dans l'onglet 'Paiements'\n";
echo "   - Vérifier les KPI de revenus\n";
echo "   - Tester les filtres de transactions\n";
echo "   - Vérifier les boutons d'action sur les paiements\n\n";

echo "9. 📱 RESPONSIVE ET MOBILITÉ:\n";
echo "   - Réduire la taille de la fenêtre\n";
echo "   - Vérifier que les onglets deviennent scrollables\n";
echo "   - Confirmer que les tableaux restent lisibles\n";
echo "   - Tester sur mobile si possible\n\n";

echo "10. 🔄 INTERACTIONS ET FEEDBACK:\n";
echo "    - Vérifier que les boutons changent au survol\n";
echo "    - Confirmer les messages de succès/erreur\n";
echo "    - Tester les confirmations de suppression\n";
echo "    - Vérifier les tooltips et les badges\n\n";

// Note sur la performance
echo "⚡ NOTE SUR LA PERFORMANCE:\n";
echo "==========================\n";
echo "Si vous constatez des lenteurs:\n";
echo "1. Ouvrir les outils de développement (F12)\n";
echo "2. Aller dans l'onglet Console\n";
echo "3. Chercher des erreurs JavaScript en rouge\n";
echo "4. Aller dans l'onglet Network pour voir les requêtes lentes\n";
echo "5. Vérifier que Chart.js se charge correctement\n\n";

// Retourner le code d'état
if (count($errors) > 0) {
    echo "🔴 RÉSULTAT: Des erreurs critiques ont été détectées\n";
    exit(1);
} elseif (count($warnings) > 5) {
    echo "🟡 RÉSULTAT: Beaucoup d'avertissements - À vérifier\n";
    exit(1);
} else {
    echo "🟢 RÉSULTAT: L'interface admin semble fonctionnelle\n";
    echo "Procédez aux tests manuels pour confirmer le bon fonctionnement.\n";
    exit(0);
}
?>
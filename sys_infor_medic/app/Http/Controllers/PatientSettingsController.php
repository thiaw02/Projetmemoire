<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientSettingsController extends Controller
{
    /**
     * Afficher la page des paramètres patient
     */
    public function index()
    {
        $user = Auth::user();
        
        // Charger les préférences de style depuis la base de données ou un fichier
        $preferences = $this->loadUserPreferences();
        
        return view('patient.settings', compact('preferences'));
    }
    
    /**
     * Sauvegarder les préférences de style
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'theme_color' => 'required|string|in:blue,purple,green,orange,red,pink',
            'card_style' => 'required|string|in:modern,classic,minimal',
            'animation_speed' => 'required|string|in:slow,normal,fast',
            'compact_mode' => 'boolean',
            'dark_mode' => 'boolean',
            'show_health_score' => 'boolean',
            'show_statistics' => 'boolean',
            'default_tab' => 'required|string|in:rdv,mesrdv,dossier,historique'
        ]);
        
        $preferences = [
            'theme_color' => $request->theme_color,
            'card_style' => $request->card_style,
            'animation_speed' => $request->animation_speed,
            'compact_mode' => $request->boolean('compact_mode'),
            'dark_mode' => $request->boolean('dark_mode'),
            'show_health_score' => $request->boolean('show_health_score'),
            'show_statistics' => $request->boolean('show_statistics'),
            'default_tab' => $request->default_tab,
            'updated_at' => now()->toISOString()
        ];
        
        $this->saveUserPreferences($preferences);
        
        return redirect()->back()->with('success', 'Vos préférences ont été sauvegardées avec succès !');
    }
    
    /**
     * Réinitialiser les préférences par défaut
     */
    public function resetPreferences()
    {
        $preferences = $this->getDefaultPreferences();
        $this->saveUserPreferences($preferences);
        
        return redirect()->back()->with('success', 'Les préférences ont été réinitialisées aux valeurs par défaut.');
    }
    
    /**
     * Charger les préférences utilisateur
     */
    private function loadUserPreferences()
    {
        $user = Auth::user();
        $preferencesPath = "preferences/patient_{$user->id}.json";
        
        if (Storage::exists($preferencesPath)) {
            $preferences = json_decode(Storage::get($preferencesPath), true);
            return array_merge($this->getDefaultPreferences(), $preferences);
        }
        
        return $this->getDefaultPreferences();
    }
    
    /**
     * Sauvegarder les préférences utilisateur
     */
    private function saveUserPreferences($preferences)
    {
        $user = Auth::user();
        $preferencesPath = "preferences/patient_{$user->id}.json";
        
        Storage::put($preferencesPath, json_encode($preferences, JSON_PRETTY_PRINT));
    }
    
    /**
     * Obtenir les préférences par défaut
     */
    private function getDefaultPreferences()
    {
        return [
            'theme_color' => 'blue',
            'card_style' => 'modern',
            'animation_speed' => 'normal',
            'compact_mode' => false,
            'dark_mode' => false,
            'show_health_score' => true,
            'show_statistics' => true,
            'default_tab' => 'rdv'
        ];
    }
    
    /**
     * API pour obtenir les préférences (utilisé par JavaScript)
     */
    public function getPreferences()
    {
        $preferences = $this->loadUserPreferences();
        return response()->json($preferences);
    }
}
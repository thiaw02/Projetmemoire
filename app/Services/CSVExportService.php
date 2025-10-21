<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class CSVExportService
{
    protected $brandingData = [
        'platform_name' => 'SMART-HEALTH',
        'address' => 'Plateforme de Santé Digitale',
        'phone' => '+221 XX XXX XX XX',
        'email' => 'contact@smart-health.sn',
        'website' => 'www.smart-health.sn'
    ];

    protected $delimiter = ';';
    protected $encoding = 'UTF-8';

    /**
     * Export des patients
     */
    public function exportPatients($patients, $options = [])
    {
        $filename = $options['filename'] ?? 'export_patients_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'ID',
            'Nom',
            'Prénom', 
            'Date de naissance',
            'Âge',
            'Sexe',
            'Téléphone',
            'Email',
            'Adresse',
            'Numéro de dossier',
            'Groupe sanguin',
            'Antécédents',
            'Date d\'inscription',
            'Statut'
        ];

        $data = $patients->map(function($patient) {
            return [
                $patient['id'],
                $patient['nom'] ?? '',
                $patient['prenom'] ?? '',
                $patient['date_naissance'] ? Carbon::parse($patient['date_naissance'])->format('d/m/Y') : '',
                $patient['date_naissance'] ? Carbon::parse($patient['date_naissance'])->age : '',
                $patient['sexe'] ?? '',
                $patient['telephone'] ?? '',
                $patient['email'] ?? '',
                $patient['adresse'] ?? '',
                $patient['numero_dossier'] ?? '',
                $patient['groupe_sanguin'] ?? '',
                $patient['antecedents'] ?? '',
                $patient['created_at'] ? Carbon::parse($patient['created_at'])->format('d/m/Y H:i') : '',
                $patient['active'] ? 'Actif' : 'Inactif'
            ];
        });

        return $this->generateCSV($data, $headers, $filename, 'LISTE DES PATIENTS');
    }

    /**
     * Export des consultations
     */
    public function exportConsultations($consultations, $options = [])
    {
        $filename = $options['filename'] ?? 'export_consultations_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'ID',
            'Patient',
            'Médecin',
            'Date consultation',
            'Type',
            'Motif',
            'Diagnostic',
            'Traitement',
            'Statut',
            'Montant',
            'Prochaine consultation'
        ];

        $data = $consultations->map(function($consultation) {
            return [
                $consultation['id'],
                ($consultation['patient']['nom'] ?? '') . ' ' . ($consultation['patient']['prenom'] ?? ''),
                'Dr. ' . ($consultation['medecin']['name'] ?? ''),
                $consultation['date_consultation'] ? Carbon::parse($consultation['date_consultation'])->format('d/m/Y H:i') : '',
                $consultation['type_consultation'] ?? '',
                $consultation['motif'] ?? '',
                $consultation['diagnostic'] ?? '',
                $consultation['traitement'] ?? '',
                $consultation['statut'] ?? '',
                $consultation['montant'] ? number_format($consultation['montant'], 0, ',', ' ') . ' FCFA' : '',
                $consultation['prochaine_consultation'] ? Carbon::parse($consultation['prochaine_consultation'])->format('d/m/Y') : ''
            ];
        });

        return $this->generateCSV($data, $headers, $filename, 'RAPPORT DES CONSULTATIONS');
    }

    /**
     * Export des rendez-vous
     */
    public function exportRendezVous($rendezvous, $options = [])
    {
        $filename = $options['filename'] ?? 'export_rendezvous_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'ID',
            'Patient',
            'Médecin',
            'Date RDV',
            'Heure',
            'Type RDV',
            'Motif',
            'Statut',
            'Date création',
            'Commentaires'
        ];

        $data = $rendezvous->map(function($rdv) {
            return [
                $rdv['id'],
                ($rdv['patient']['nom'] ?? '') . ' ' . ($rdv['patient']['prenom'] ?? ''),
                'Dr. ' . ($rdv['medecin']['name'] ?? ''),
                $rdv['date'] ? Carbon::parse($rdv['date'])->format('d/m/Y') : '',
                $rdv['heure'] ?? '',
                $rdv['type_rdv'] ?? 'Consultation',
                $rdv['motif'] ?? '',
                $rdv['statut'] ?? '',
                $rdv['created_at'] ? Carbon::parse($rdv['created_at'])->format('d/m/Y H:i') : '',
                $rdv['commentaires'] ?? ''
            ];
        });

        return $this->generateCSV($data, $headers, $filename, 'PLANNING DES RENDEZ-VOUS');
    }

    /**
     * Export des paiements
     */
    public function exportPaiements($paiements, $options = [])
    {
        $filename = $options['filename'] ?? 'export_paiements_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'ID',
            'Patient',
            'Date paiement',
            'Mode paiement',
            'Montant',
            'Statut',
            'Référence',
            'Type prestation',
            'Caissier',
            'Commentaires'
        ];

        $data = $paiements->map(function($paiement) {
            return [
                $paiement['id'],
                ($paiement['patient']['nom'] ?? '') . ' ' . ($paiement['patient']['prenom'] ?? ''),
                $paiement['date_paiement'] ? Carbon::parse($paiement['date_paiement'])->format('d/m/Y H:i') : '',
                $paiement['mode_paiement'] ?? '',
                number_format($paiement['montant'], 0, ',', ' ') . ' FCFA',
                $paiement['statut'] ?? '',
                $paiement['reference'] ?? '',
                $paiement['type_prestation'] ?? '',
                $paiement['caissier'] ?? '',
                $paiement['commentaires'] ?? ''
            ];
        });

        return $this->generateCSV($data, $headers, $filename, 'RAPPORT DES PAIEMENTS');
    }

    /**
     * Export des analyses
     */
    public function exportAnalyses($analyses, $options = [])
    {
        $filename = $options['filename'] ?? 'export_analyses_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'ID',
            'Patient',
            'Type analyse',
            'Date prélèvement',
            'Date analyse',
            'Statut',
            'Médecin prescripteur',
            'Laboratoire',
            'Résultats anormaux',
            'Interprétation'
        ];

        $data = $analyses->map(function($analyse) {
            $resultatsAnormaux = collect($analyse['resultats'] ?? [])
                ->filter(fn($r) => $r['statut'] === 'Anormal')
                ->count();

            return [
                $analyse['id'],
                ($analyse['patient']['nom'] ?? '') . ' ' . ($analyse['patient']['prenom'] ?? ''),
                $analyse['type_analyse'] ?? '',
                $analyse['date_prelevement'] ? Carbon::parse($analyse['date_prelevement'])->format('d/m/Y H:i') : '',
                $analyse['date_analyse'] ? Carbon::parse($analyse['date_analyse'])->format('d/m/Y H:i') : '',
                $analyse['statut'] ?? '',
                'Dr. ' . ($analyse['medecin_prescripteur'] ?? ''),
                $analyse['laboratoire'] ?? '',
                $resultatsAnormaux . ' sur ' . count($analyse['resultats'] ?? []),
                $analyse['interpretation'] ?? ''
            ];
        });

        return $this->generateCSV($data, $headers, $filename, 'RAPPORT DES ANALYSES MÉDICALES');
    }

    /**
     * Export des utilisateurs
     */
    public function exportUtilisateurs($users, $options = [])
    {
        $filename = $options['filename'] ?? 'export_utilisateurs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'ID',
            'Nom',
            'Email',
            'Rôle',
            'Téléphone professionnel',
            'Spécialité',
            'Date inscription',
            'Dernière connexion',
            'Statut'
        ];

        $data = $users->map(function($user) {
            return [
                $user['id'],
                $user['name'] ?? '',
                $user['email'] ?? '',
                $user['role'] ?? '',
                $user['pro_phone'] ?? '',
                $user['specialite'] ?? '',
                $user['created_at'] ? Carbon::parse($user['created_at'])->format('d/m/Y H:i') : '',
                $user['last_login_at'] ? Carbon::parse($user['last_login_at'])->format('d/m/Y H:i') : 'Jamais',
                $user['active'] ? 'Actif' : 'Inactif'
            ];
        });

        return $this->generateCSV($data, $headers, $filename, 'LISTE DES UTILISATEURS');
    }

    /**
     * Export des statistiques
     */
    public function exportStatistiques($stats, $options = [])
    {
        $filename = $options['filename'] ?? 'export_statistiques_' . date('Y-m-d_H-i-s') . '.csv';
        
        $data = new Collection([]);
        
        // Ajouter les données de statistiques
        if (isset($stats['patients'])) {
            $data->push(['Métrique', 'Total Patients', $stats['patients']['total'] ?? 0]);
            $data->push(['Métrique', 'Nouveaux patients ce mois', $stats['patients']['nouveaux_mois'] ?? 0]);
            $data->push(['Métrique', 'Patients actifs', $stats['patients']['actifs'] ?? 0]);
        }

        if (isset($stats['consultations'])) {
            $data->push(['Métrique', 'Total Consultations', $stats['consultations']['total'] ?? 0]);
            $data->push(['Métrique', 'Consultations ce mois', $stats['consultations']['ce_mois'] ?? 0]);
            $data->push(['Métrique', 'Consultations aujourd\'hui', $stats['consultations']['aujourdhui'] ?? 0]);
        }

        if (isset($stats['revenus'])) {
            $data->push(['Métrique', 'Revenus totaux (FCFA)', number_format($stats['revenus']['total'] ?? 0, 0, ',', ' ')]);
            $data->push(['Métrique', 'Revenus ce mois (FCFA)', number_format($stats['revenus']['ce_mois'] ?? 0, 0, ',', ' ')]);
        }

        $headers = ['Type', 'Description', 'Valeur'];

        return $this->generateCSV($data, $headers, $filename, 'STATISTIQUES GÉNÉRALES');
    }

    /**
     * Générer le fichier CSV avec en-tête personnalisé
     */
    private function generateCSV($data, $headers, $filename, $title)
    {
        $content = '';
        
        // En-tête de l'entreprise
        $content .= '"' . $this->brandingData['platform_name'] . '"' . "\n";
        $content .= '"' . $this->brandingData['address'] . '"' . "\n";
        $content .= '"Tél: ' . $this->brandingData['phone'] . ' - Email: ' . $this->brandingData['email'] . '"' . "\n";
        $content .= '"' . $this->brandingData['website'] . '"' . "\n";
        $content .= "\n";
        
        // Titre du document
        $content .= '"' . $title . '"' . "\n";
        $content .= '"Généré le ' . Carbon::now()->format('d/m/Y à H:i') . '"' . "\n";
        $content .= '"Nombre d\'enregistrements: ' . $data->count() . '"' . "\n";
        $content .= "\n";
        
        // En-têtes des colonnes
        $content .= implode($this->delimiter, array_map(function($header) {
            return '"' . $header . '"';
        }, $headers)) . "\n";
        
        // Données
        foreach ($data as $row) {
            $escapedRow = array_map(function($value) {
                // Échapper les guillemets et gérer les valeurs nulles
                $value = $value ?? '';
                $value = str_replace('"', '""', $value);
                return '"' . $value . '"';
            }, is_array($row) ? $row : $row->toArray());
            
            $content .= implode($this->delimiter, $escapedRow) . "\n";
        }
        
        // Pied de page
        $content .= "\n";
        $content .= '"--- Fin du rapport ---"' . "\n";
        $content .= '"Document généré par ' . $this->brandingData['platform_name'] . '"' . "\n";
        
        return [
            'content' => $content,
            'filename' => $filename,
            'mimetype' => 'text/csv',
            'encoding' => $this->encoding
        ];
    }

    /**
     * Sauvegarder le CSV sur le serveur
     */
    public function saveCSV($csvData, $path = null)
    {
        $path = $path ?? storage_path('app/exports/' . $csvData['filename']);
        
        // Créer le répertoire s'il n'existe pas
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Écrire le fichier avec l'encodage UTF-8 + BOM pour Excel
        $bom = "\xEF\xBB\xBF";
        file_put_contents($path, $bom . $csvData['content']);
        
        return $path;
    }

    /**
     * Télécharger le CSV directement
     */
    public function downloadCSV($csvData)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $csvData['filename'] . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        return response($csvData['content'], 200, $headers);
    }

    /**
     * Configuration personnalisée
     */
    public function setConfig($config)
    {
        if (isset($config['delimiter'])) {
            $this->delimiter = $config['delimiter'];
        }
        
        if (isset($config['encoding'])) {
            $this->encoding = $config['encoding'];
        }
        
        if (isset($config['branding'])) {
            $this->brandingData = array_merge($this->brandingData, $config['branding']);
        }
        
        return $this;
    }

    /**
     * Obtenir les informations de branding
     */
    public function getBrandingData()
    {
        return $this->brandingData;
    }
}
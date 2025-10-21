<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PDFGeneratorService;
use App\Services\CSVExportService;
use Carbon\Carbon;

class ExportController extends Controller
{
    protected $pdfService;
    protected $csvService;

    public function __construct(PDFGeneratorService $pdfService, CSVExportService $csvService)
    {
        $this->pdfService = $pdfService;
        $this->csvService = $csvService;
    }

    /**
     * Export des patients en PDF
     */
    public function exportPatientsPDF(Request $request)
    {
        // Récupérer les patients (remplacer par votre logique)
        $patients = $this->getPatients($request);
        
        $reportData = [
            'title' => 'Liste des Patients',
            'date' => Carbon::now(),
            'patients' => $patients,
            'total' => $patients->count(),
            'filters' => $request->only(['date_debut', 'date_fin', 'sexe', 'age_min', 'age_max'])
        ];

        return $this->pdfService->generateRapportStatsPDF($reportData);
    }

    /**
     * Export des patients en CSV
     */
    public function exportPatientsCSV(Request $request)
    {
        $patients = $this->getPatients($request);
        
        $csvData = $this->csvService->exportPatients($patients, [
            'filename' => 'patients_' . date('Y-m-d_H-i-s') . '.csv'
        ]);

        return $this->csvService->downloadCSV($csvData);
    }

    /**
     * Export des consultations en PDF
     */
    public function exportConsultationsPDF(Request $request)
    {
        $consultations = $this->getConsultations($request);
        
        $reportData = [
            'title' => 'Rapport des Consultations',
            'date' => Carbon::now(),
            'consultations' => $consultations,
            'total' => $consultations->count(),
            'periode' => [
                'debut' => $request->get('date_debut', Carbon::now()->startOfMonth()->format('Y-m-d')),
                'fin' => $request->get('date_fin', Carbon::now()->format('Y-m-d'))
            ],
            'statistiques' => [
                'total_montant' => $consultations->sum('montant'),
                'consultations_par_statut' => $consultations->groupBy('statut')->map->count(),
                'consultations_par_medecin' => $consultations->groupBy('medecin.name')->map->count()
            ]
        ];

        return $this->pdfService->generateRapportStatsPDF($reportData);
    }

    /**
     * Export des consultations en CSV
     */
    public function exportConsultationsCSV(Request $request)
    {
        $consultations = $this->getConsultations($request);
        
        $csvData = $this->csvService->exportConsultations($consultations, [
            'filename' => 'consultations_' . date('Y-m-d_H-i-s') . '.csv'
        ]);

        return $this->csvService->downloadCSV($csvData);
    }

    /**
     * Export des rendez-vous en PDF
     */
    public function exportRendezVousPDF(Request $request)
    {
        $rendezvous = $this->getRendezVous($request);
        
        $planningData = [
            'title' => 'Planning des Rendez-vous',
            'date' => Carbon::now(),
            'periode' => [
                'debut' => $request->get('date_debut', Carbon::now()->format('Y-m-d')),
                'fin' => $request->get('date_fin', Carbon::now()->addWeek()->format('Y-m-d'))
            ],
            'rendezvous' => $rendezvous,
            'medecins' => $rendezvous->pluck('medecin')->unique('id'),
            'statistiques' => [
                'total' => $rendezvous->count(),
                'confirmes' => $rendezvous->where('statut', 'Confirmé')->count(),
                'en_attente' => $rendezvous->where('statut', 'En attente')->count(),
                'annules' => $rendezvous->where('statut', 'Annulé')->count()
            ]
        ];

        return $this->pdfService->generatePlanningPDF($planningData);
    }

    /**
     * Export des rendez-vous en CSV
     */
    public function exportRendezVousCSV(Request $request)
    {
        $rendezvous = $this->getRendezVous($request);
        
        $csvData = $this->csvService->exportRendezVous($rendezvous, [
            'filename' => 'rendezvous_' . date('Y-m-d_H-i-s') . '.csv'
        ]);

        return $this->csvService->downloadCSV($csvData);
    }

    /**
     * Export des paiements en CSV
     */
    public function exportPaiementsCSV(Request $request)
    {
        $paiements = $this->getPaiements($request);
        
        $csvData = $this->csvService->exportPaiements($paiements, [
            'filename' => 'paiements_' . date('Y-m-d_H-i-s') . '.csv'
        ]);

        return $this->csvService->downloadCSV($csvData);
    }

    /**
     * Export des analyses en CSV
     */
    public function exportAnalysesCSV(Request $request)
    {
        $analyses = $this->getAnalyses($request);
        
        $csvData = $this->csvService->exportAnalyses($analyses, [
            'filename' => 'analyses_' . date('Y-m-d_H-i-s') . '.csv'
        ]);

        return $this->csvService->downloadCSV($csvData);
    }

    /**
     * Export des utilisateurs en CSV
     */
    public function exportUtilisateursCSV(Request $request)
    {
        $users = $this->getUtilisateurs($request);
        
        $csvData = $this->csvService->exportUtilisateurs($users, [
            'filename' => 'utilisateurs_' . date('Y-m-d_H-i-s') . '.csv'
        ]);

        return $this->csvService->downloadCSV($csvData);
    }

    /**
     * Export des statistiques générales en PDF et CSV
     */
    public function exportStatistiquesPDF(Request $request)
    {
        $stats = $this->getStatistiques($request);
        
        return $this->pdfService->generateRapportStatsPDF($stats);
    }

    public function exportStatistiquesCSV(Request $request)
    {
        $stats = $this->getStatistiques($request);
        
        $csvData = $this->csvService->exportStatistiques($stats, [
            'filename' => 'statistiques_' . date('Y-m-d_H-i-s') . '.csv'
        ]);

        return $this->csvService->downloadCSV($csvData);
    }

    /**
     * Export combiné - PDF et CSV en zip
     */
    public function exportCombine(Request $request)
    {
        $type = $request->get('type', 'patients');
        
        switch($type) {
            case 'patients':
                $data = $this->getPatients($request);
                $pdfData = [
                    'title' => 'Liste des Patients',
                    'date' => Carbon::now(),
                    'patients' => $data,
                    'total' => $data->count()
                ];
                $pdf = $this->pdfService->generateRapportStatsPDF($pdfData);
                $csv = $this->csvService->exportPatients($data);
                break;
                
            case 'consultations':
                $data = $this->getConsultations($request);
                $pdfData = [
                    'title' => 'Rapport des Consultations',
                    'consultations' => $data
                ];
                $pdf = $this->pdfService->generateRapportStatsPDF($pdfData);
                $csv = $this->csvService->exportConsultations($data);
                break;
                
            default:
                return response()->json(['error' => 'Type d\'export non supporté'], 400);
        }

        // Créer un fichier ZIP contenant PDF et CSV
        $zipFilename = $type . '_export_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);
        
        // Créer le répertoire temporaire
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            // Sauvegarder temporairement les fichiers
            $pdfPath = storage_path('app/temp/' . str_replace('.pdf', '_temp.pdf', basename($pdf->getOriginalContent())));
            $csvPath = storage_path('app/temp/' . $csv['filename']);
            
            // Écrire les fichiers temporaires
            file_put_contents($pdfPath, $pdf->getContent());
            $this->csvService->saveCSV($csv, $csvPath);
            
            // Ajouter au ZIP
            $zip->addFile($pdfPath, basename($pdfPath));
            $zip->addFile($csvPath, basename($csvPath));
            $zip->close();
            
            // Nettoyer les fichiers temporaires
            unlink($pdfPath);
            unlink($csvPath);
            
            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
        }
        
        return response()->json(['error' => 'Impossible de créer le fichier ZIP'], 500);
    }

    // Méthodes privées pour récupérer les données (à adapter selon votre modèle)
    
    private function getPatients($request)
    {
        // Remplacer par votre logique de récupération des patients
        // return Patient::with(['consultations', 'analyses'])->get();
        
        // Exemple de données fictives pour les tests
        return collect([
            [
                'id' => 1,
                'nom' => 'Diallo',
                'prenom' => 'Fatou',
                'date_naissance' => '1985-03-15',
                'sexe' => 'F',
                'telephone' => '+221 77 123 45 67',
                'email' => 'fatou.diallo@email.com',
                'adresse' => 'Dakar, Plateau',
                'numero_dossier' => 'PAT001',
                'groupe_sanguin' => 'O+',
                'antecedents' => 'Hypertension',
                'created_at' => '2024-01-15',
                'active' => true
            ],
            // Ajouter plus de données...
        ]);
    }

    private function getConsultations($request)
    {
        // return Consultation::with(['patient', 'medecin'])->get();
        
        return collect([
            [
                'id' => 1,
                'patient' => ['nom' => 'Diallo', 'prenom' => 'Fatou'],
                'medecin' => ['name' => 'Sall'],
                'date_consultation' => '2024-01-20 10:30:00',
                'type_consultation' => 'Consultation générale',
                'motif' => 'Contrôle tension',
                'diagnostic' => 'Hypertension artérielle',
                'traitement' => 'Amlodipine 5mg',
                'statut' => 'Terminée',
                'montant' => 25000,
                'prochaine_consultation' => '2024-02-20'
            ]
        ]);
    }

    private function getRendezVous($request)
    {
        // return RendezVous::with(['patient', 'medecin'])->get();
        
        return collect([
            [
                'id' => 1,
                'patient' => ['nom' => 'Diallo', 'prenom' => 'Fatou'],
                'medecin' => ['name' => 'Sall'],
                'date' => '2024-01-25',
                'heure' => '14:30',
                'type_rdv' => 'Consultation',
                'motif' => 'Suivi hypertension',
                'statut' => 'Confirmé',
                'created_at' => '2024-01-22',
                'commentaires' => 'Patient préfère l\'après-midi'
            ]
        ]);
    }

    private function getPaiements($request)
    {
        return collect([
            [
                'id' => 1,
                'patient' => ['nom' => 'Diallo', 'prenom' => 'Fatou'],
                'date_paiement' => '2024-01-20 10:30:00',
                'mode_paiement' => 'Espèces',
                'montant' => 25000,
                'statut' => 'Payé',
                'reference' => 'PAY001',
                'type_prestation' => 'Consultation',
                'caissier' => 'Réceptionniste',
                'commentaires' => ''
            ]
        ]);
    }

    private function getAnalyses($request)
    {
        return collect([
            [
                'id' => 1,
                'patient' => ['nom' => 'Diallo', 'prenom' => 'Fatou'],
                'type_analyse' => 'Bilan lipidique',
                'date_prelevement' => '2024-01-18 08:00:00',
                'date_analyse' => '2024-01-18 16:00:00',
                'statut' => 'Terminé',
                'medecin_prescripteur' => 'Sall',
                'laboratoire' => 'Lab Central',
                'resultats' => [
                    ['parametre' => 'Cholestérol', 'valeur' => '2.8', 'statut' => 'Normal'],
                    ['parametre' => 'Triglycérides', 'valeur' => '1.8', 'statut' => 'Élevé']
                ],
                'interpretation' => 'Légère élévation des triglycérides'
            ]
        ]);
    }

    private function getUtilisateurs($request)
    {
        return collect([
            [
                'id' => 1,
                'name' => 'Dr. Sall',
                'email' => 'dr.sall@smart-health.sn',
                'role' => 'Médecin',
                'pro_phone' => '+221 77 987 65 43',
                'specialite' => 'Médecine générale',
                'created_at' => '2024-01-01',
                'last_login_at' => '2024-01-20 08:30:00',
                'active' => true
            ]
        ]);
    }

    private function getStatistiques($request)
    {
        return [
            'title' => 'Statistiques Générales',
            'date' => Carbon::now(),
            'periode' => [
                'debut' => $request->get('date_debut', Carbon::now()->startOfMonth()),
                'fin' => $request->get('date_fin', Carbon::now())
            ],
            'patients' => [
                'total' => 1250,
                'nouveaux_mois' => 45,
                'actifs' => 1180
            ],
            'consultations' => [
                'total' => 2340,
                'ce_mois' => 195,
                'aujourdhui' => 12
            ],
            'revenus' => [
                'total' => 15750000,
                'ce_mois' => 1280000
            ]
        ];
    }
}
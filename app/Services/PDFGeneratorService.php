<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PDFGeneratorService
{
    protected $defaultSettings = [
        'paper' => 'A4',
        'orientation' => 'portrait',
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_left' => 15,
        'margin_right' => 15,
    ];

    protected $brandingData = [
        'platform_name' => 'SMART-HEALTH',
        'logo_path' => 'images/LOGO PLATEFORME.png',
        'primary_color' => '#10b981',
        'secondary_color' => '#059669',
        'accent_color' => '#047857',
        'address' => 'Plateforme de Santé Digitale',
        'phone' => '+221 XX XXX XX XX',
        'email' => 'contact@smart-health.sn',
        'website' => 'www.smart-health.sn'
    ];

    /**
     * Générer une ordonnance PDF
     */
    public function generateOrdonnancePDF($ordonnanceData)
    {
        $data = array_merge($this->brandingData, [
            'title' => 'ORDONNANCE MÉDICALE',
            'document_type' => 'ordonnance',
            'ordonnance' => $ordonnanceData,
            'generated_at' => Carbon::now()->format('d/m/Y à H:i'),
            'document_number' => 'ORD-' . str_pad($ordonnanceData['id'], 6, '0', STR_PAD_LEFT)
        ]);

        $pdf = PDF::loadView('pdf.ordonnance', $data);
        $pdf->setPaper($this->defaultSettings['paper'], $this->defaultSettings['orientation']);
        
        return $pdf;
    }

    /**
     * Générer un rapport de consultation PDF
     */
    public function generateConsultationPDF($consultationData)
    {
        $data = array_merge($this->brandingData, [
            'title' => 'RAPPORT DE CONSULTATION',
            'document_type' => 'consultation',
            'consultation' => $consultationData,
            'generated_at' => Carbon::now()->format('d/m/Y à H:i'),
            'document_number' => 'CONS-' . str_pad($consultationData['id'], 6, '0', STR_PAD_LEFT)
        ]);

        $pdf = PDF::loadView('pdf.consultation', $data);
        $pdf->setPaper($this->defaultSettings['paper'], $this->defaultSettings['orientation']);
        
        return $pdf;
    }

    /**
     * Générer un résultat d'analyse PDF
     */
    public function generateAnalysePDF($analyseData)
    {
        $data = array_merge($this->brandingData, [
            'title' => 'RÉSULTATS D\'ANALYSES MÉDICALES',
            'document_type' => 'analyse',
            'analyse' => $analyseData,
            'generated_at' => Carbon::now()->format('d/m/Y à H:i'),
            'document_number' => 'ANA-' . str_pad($analyseData['id'], 6, '0', STR_PAD_LEFT)
        ]);

        $pdf = PDF::loadView('pdf.analyse', $data);
        $pdf->setPaper($this->defaultSettings['paper'], $this->defaultSettings['orientation']);
        
        return $pdf;
    }

    /**
     * Générer un ticket/reçu PDF
     */
    public function generateTicketPDF($ticketData)
    {
        $data = array_merge($this->brandingData, [
            'title' => 'REÇU DE PAIEMENT',
            'document_type' => 'ticket',
            'ticket' => $ticketData,
            'generated_at' => Carbon::now()->format('d/m/Y à H:i'),
            'document_number' => 'TIC-' . str_pad($ticketData['id'], 6, '0', STR_PAD_LEFT)
        ]);

        $pdf = PDF::loadView('pdf.ticket', $data);
        $pdf->setPaper($this->defaultSettings['paper'], $this->defaultSettings['orientation']);
        
        return $pdf;
    }

    /**
     * Générer un certificat médical PDF
     */
    public function generateCertificatPDF($certificatData)
    {
        $data = array_merge($this->brandingData, [
            'title' => 'CERTIFICAT MÉDICAL',
            'document_type' => 'certificat',
            'certificat' => $certificatData,
            'generated_at' => Carbon::now()->format('d/m/Y à H:i'),
            'document_number' => 'CERT-' . str_pad($certificatData['id'], 6, '0', STR_PAD_LEFT)
        ]);

        $pdf = PDF::loadView('pdf.certificat', $data);
        $pdf->setPaper($this->defaultSettings['paper'], $this->defaultSettings['orientation']);
        
        return $pdf;
    }

    /**
     * Générer un rapport de statistiques PDF
     */
    public function generateRapportStatsPDF($statsData)
    {
        $data = array_merge($this->brandingData, [
            'title' => 'RAPPORT STATISTIQUE',
            'document_type' => 'stats',
            'stats' => $statsData,
            'generated_at' => Carbon::now()->format('d/m/Y à H:i'),
            'document_number' => 'STAT-' . Carbon::now()->format('Ymd-His')
        ]);

        $pdf = PDF::loadView('pdf.stats', $data);
        $pdf->setPaper($this->defaultSettings['paper'], $this->defaultSettings['orientation']);
        
        return $pdf;
    }

    /**
     * Générer un planning PDF
     */
    public function generatePlanningPDF($planningData)
    {
        $data = array_merge($this->brandingData, [
            'title' => 'PLANNING MÉDICAL',
            'document_type' => 'planning',
            'planning' => $planningData,
            'generated_at' => Carbon::now()->format('d/m/Y à H:i'),
            'document_number' => 'PLAN-' . Carbon::now()->format('Ymd')
        ]);

        $pdf = PDF::loadView('pdf.planning', $data);
        $pdf->setPaper($this->defaultSettings['paper'], 'landscape'); // Paysage pour planning
        
        return $pdf;
    }

    /**
     * Obtenir les données de branding
     */
    public function getBrandingData()
    {
        return $this->brandingData;
    }

    /**
     * Définir des paramètres personnalisés
     */
    public function setCustomSettings($settings)
    {
        $this->defaultSettings = array_merge($this->defaultSettings, $settings);
        return $this;
    }
}
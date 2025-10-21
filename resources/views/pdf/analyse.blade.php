@extends('pdf.layout.base')

@section('content')
<div class="content-section">
    <!-- Informations Patient -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">INFORMATIONS PATIENT</h3>
        <div class="info-row">
            <span class="info-label">Nom complet:</span>
            <span class="info-value font-bold">{{ $analyse['patient']['nom'] }} {{ $analyse['patient']['prenom'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de naissance:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($analyse['patient']['date_naissance'])->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Âge:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($analyse['patient']['date_naissance'])->age }} ans</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sexe:</span>
            <span class="info-value">{{ $analyse['patient']['sexe'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Numéro de dossier:</span>
            <span class="info-value font-bold">{{ $analyse['patient']['numero_dossier'] ?? 'Non assigné' }}</span>
        </div>
    </div>

    <!-- Informations Analyse -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">DÉTAILS DE L'ANALYSE</h3>
        <div class="info-row">
            <span class="info-label">Date de prélèvement:</span>
            <span class="info-value font-bold">{{ \Carbon\Carbon::parse($analyse['date_prelevement'])->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date d'analyse:</span>
            <span class="info-value font-bold">{{ \Carbon\Carbon::parse($analyse['date_analyse'])->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Type d'analyse:</span>
            <span class="info-value">{{ $analyse['type_analyse'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Laboratoire:</span>
            <span class="info-value">{{ $analyse['laboratoire'] ?? 'Laboratoire SMART-HEALTH' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Médecin prescripteur:</span>
            <span class="info-value">Dr. {{ $analyse['medecin_prescripteur'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Technicien:</span>
            <span class="info-value">{{ $analyse['technicien'] ?? 'Non renseigné' }}</span>
        </div>
    </div>

    <!-- Statut de l'analyse -->
    <div class="content-section mb-4">
        <div class="info-row" style="text-align: center;">
            <span class="font-bold text-lg">STATUT DE L'ANALYSE: </span>
            <span class="font-bold text-lg @if($analyse['statut'] == 'Terminé') text-success @elseif($analyse['statut'] == 'En cours') text-warning @else text-info @endif">
                {{ strtoupper($analyse['statut']) }}
            </span>
        </div>
    </div>

    <!-- Indication de l'examen -->
    @if(isset($analyse['indication']) && $analyse['indication'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">INDICATION DE L'EXAMEN</h3>
        <div class="highlight-box">
            <p>{{ $analyse['indication'] }}</p>
        </div>
    </div>
    @endif

    <!-- Résultats d'analyses -->
    @if(isset($analyse['resultats']) && count($analyse['resultats']) > 0)
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">RÉSULTATS D'ANALYSES</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Paramètre</th>
                    <th>Résultat</th>
                    <th>Unité</th>
                    <th>Valeurs de référence</th>
                    <th>Statut</th>
                    <th>Observations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analyse['resultats'] as $resultat)
                <tr class="@if($resultat['statut'] == 'Anormal') alert-row @endif">
                    <td class="font-bold">{{ $resultat['parametre'] }}</td>
                    <td class="@if($resultat['statut'] == 'Normal') text-success @elseif($resultat['statut'] == 'Anormal') text-danger @else text-warning @endif font-bold">
                        {{ $resultat['valeur'] }}
                    </td>
                    <td>{{ $resultat['unite'] }}</td>
                    <td>{{ $resultat['valeurs_reference'] }}</td>
                    <td>
                        <span class="status-badge @if($resultat['statut'] == 'Normal') status-normal @elseif($resultat['statut'] == 'Anormal') status-anormal @else status-limite @endif">
                            {{ $resultat['statut'] }}
                        </span>
                    </td>
                    <td class="text-sm">{{ $resultat['observations'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Résultats anormaux (mise en évidence) -->
    @php
        $resultatsAnormaux = collect($analyse['resultats'])->filter(function($resultat) {
            return $resultat['statut'] === 'Anormal';
        });
    @endphp

    @if($resultatsAnormaux->count() > 0)
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #ef4444;">⚠️ RÉSULTATS ANORMAUX À NOTER</h3>
        <div class="alert-box">
            @foreach($resultatsAnormaux as $anormal)
            <div class="anomalie-item">
                <strong>{{ $anormal['parametre'] }}:</strong> 
                {{ $anormal['valeur'] }} {{ $anormal['unite'] }} 
                (Normal: {{ $anormal['valeurs_reference'] }})
                @if($anormal['observations'])
                <br><em>→ {{ $anormal['observations'] }}</em>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Interprétation générale -->
    @if(isset($analyse['interpretation']) && $analyse['interpretation'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">INTERPRÉTATION GÉNÉRALE</h3>
        <div class="highlight-box">
            <p>{{ $analyse['interpretation'] }}</p>
        </div>
    </div>
    @endif

    <!-- Recommandations -->
    @if(isset($analyse['recommandations']) && $analyse['recommandations'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">RECOMMANDATIONS</h3>
        <div class="warning-box">
            <p>{{ $analyse['recommandations'] }}</p>
        </div>
    </div>
    @endif

    <!-- Examens complémentaires suggérés -->
    @if(isset($analyse['examens_complementaires']) && count($analyse['examens_complementaires']) > 0)
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">EXAMENS COMPLÉMENTAIRES SUGGÉRÉS</h3>
        <div class="highlight-box">
            <ul style="list-style-type: disc; padding-left: 20px;">
                @foreach($analyse['examens_complementaires'] as $examen)
                <li>{{ $examen['nom'] }} - {{ $examen['motif'] }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Conditions de prélèvement -->
    @if(isset($analyse['conditions_prelevement']) && $analyse['conditions_prelevement'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">CONDITIONS DE PRÉLÈVEMENT</h3>
        <div class="highlight-box">
            <p>{{ $analyse['conditions_prelevement'] }}</p>
        </div>
    </div>
    @endif

    <!-- Méthodes analytiques -->
    @if(isset($analyse['methodes']) && count($analyse['methodes']) > 0)
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #8b5cf6;">MÉTHODES ANALYTIQUES</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Paramètre</th>
                    <th>Méthode</th>
                    <th>Équipement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analyse['methodes'] as $methode)
                <tr>
                    <td>{{ $methode['parametre'] }}</td>
                    <td>{{ $methode['methode'] }}</td>
                    <td>{{ $methode['equipement'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Résumé de l'analyse -->
    <div class="warning-box mt-4">
        <h4 class="font-bold mb-2">🔬 RÉSUMÉ DE L'ANALYSE</h4>
        <p><strong>Patient:</strong> {{ $analyse['patient']['nom'] }} {{ $analyse['patient']['prenom'] }} ({{ \Carbon\Carbon::parse($analyse['patient']['date_naissance'])->age }} ans)</p>
        <p><strong>Type d'analyse:</strong> {{ $analyse['type_analyse'] }}</p>
        <p><strong>Date de prélèvement:</strong> {{ \Carbon\Carbon::parse($analyse['date_prelevement'])->format('d/m/Y à H:i') }}</p>
        <p><strong>Nombre de paramètres analysés:</strong> {{ count($analyse['resultats']) }}</p>
        <p><strong>Résultats anormaux:</strong> {{ $resultatsAnormaux->count() }}</p>
        <p><strong>Statut:</strong> {{ $analyse['statut'] }}</p>
    </div>

    <!-- Notes importantes -->
    <div class="alert-box mt-4">
        <h4 class="font-bold mb-2">📋 NOTES IMPORTANTES</h4>
        <ul style="list-style-type: disc; padding-left: 20px;">
            <li>Ces résultats doivent être interprétés dans le contexte clinique du patient</li>
            <li>Les valeurs de référence peuvent varier selon l'âge, le sexe et la population</li>
            <li>En cas de résultats anormaux, consulter rapidement votre médecin traitant</li>
            <li>Ce document ne remplace pas une consultation médicale</li>
            <li>Conserver ce rapport pour le suivi médical ultérieur</li>
            @if(isset($analyse['validite']) && $analyse['validite'])
            <li>Résultats valables jusqu'au {{ \Carbon\Carbon::parse($analyse['validite'])->format('d/m/Y') }}</li>
            @endif
        </ul>
    </div>

    <!-- Validation et signatures -->
    <div class="content-section mt-4">
        <div style="display: flex; justify-content: space-between;">
            <!-- Signature technicien -->
            <div style="text-align: left; width: 48%;">
                <p><strong>Technicien de laboratoire</strong></p>
                <div style="height: 60px; border-bottom: 1px solid #ccc; margin-top: 10px;"></div>
                <p class="text-sm mt-2">{{ $analyse['technicien'] ?? 'Technicien qualifié' }}</p>
                <p class="text-sm">{{ \Carbon\Carbon::parse($analyse['date_analyse'])->format('d/m/Y à H:i') }}</p>
            </div>
            
            <!-- Signature biologiste -->
            <div style="text-align: right; width: 48%;">
                <p><strong>Biologiste responsable</strong></p>
                <div style="height: 60px; border-bottom: 1px solid #ccc; margin-top: 10px;"></div>
                <p class="text-sm mt-2">{{ $analyse['biologiste'] ?? 'Dr. Biologiste' }}</p>
                <p class="text-sm">Validation et contrôle qualité</p>
            </div>
        </div>
    </div>
</div>

@push('pdf-styles')
<style>
    /* Styles spécifiques aux analyses */
    .document-type-analyse .data-table th {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }
    
    .document-type-analyse .highlight-box {
        border-left-color: #8b5cf6;
        background: linear-gradient(135deg, #8b5cf610, #7c3aed05);
    }
    
    /* Statuts des résultats */
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .status-normal {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }
    
    .status-anormal {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }
    
    .status-limite {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    
    /* Mise en évidence des résultats anormaux */
    .alert-row {
        background-color: #fef2f2 !important;
    }
    
    .alert-row td {
        border-left: 3px solid #ef4444;
    }
    
    /* Anomalies importantes */
    .anomalie-item {
        padding: 8px;
        margin-bottom: 8px;
        background: #fee2e2;
        border-left: 4px solid #ef4444;
        border-radius: 0 6px 6px 0;
        font-size: 11px;
    }
    
    /* Graphiques de tendance (si nécessaire) */
    .trend-up {
        color: #ef4444;
        font-weight: bold;
    }
    
    .trend-down {
        color: #10b981;
        font-weight: bold;
    }
    
    .trend-stable {
        color: #6b7280;
        font-weight: bold;
    }
    
    /* Valeurs critiques */
    .valeur-critique {
        background: #7f1d1d;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
    }
    
    /* Méthodes analytiques */
    .methode-info {
        font-size: 9px;
        color: #6b7280;
        font-style: italic;
    }
    
    /* Sections spécialisées par type d'analyse */
    .analyse-hematologie .data-table th {
        background: linear-gradient(135deg, #dc2626, #991b1b);
    }
    
    .analyse-biochimie .data-table th {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    .analyse-microbiologie .data-table th {
        background: linear-gradient(135deg, #7c2d12, #92400e);
    }
    
    .analyse-immunologie .data-table th {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
    }
</style>
@endpush
@endsection
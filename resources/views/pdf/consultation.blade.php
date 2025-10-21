@extends('pdf.layout.base')

@section('content')
<div class="content-section">
    <!-- Informations Patient -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">INFORMATIONS PATIENT</h3>
        <div class="info-row">
            <span class="info-label">Nom complet:</span>
            <span class="info-value font-bold">{{ $consultation['patient']['nom'] }} {{ $consultation['patient']['prenom'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de naissance:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($consultation['patient']['date_naissance'])->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Âge:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($consultation['patient']['date_naissance'])->age }} ans</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sexe:</span>
            <span class="info-value">{{ $consultation['patient']['sexe'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Numéro de dossier:</span>
            <span class="info-value font-bold">{{ $consultation['patient']['numero_dossier'] ?? 'Non assigné' }}</span>
        </div>
    </div>

    <!-- Informations Consultation -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">DÉTAILS DE LA CONSULTATION</h3>
        <div class="info-row">
            <span class="info-label">Date de consultation:</span>
            <span class="info-value font-bold">{{ \Carbon\Carbon::parse($consultation['date_consultation'])->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Médecin consultant:</span>
            <span class="info-value font-bold">Dr. {{ $consultation['medecin']['name'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Spécialité:</span>
            <span class="info-value">{{ $consultation['medecin']['specialite'] ?? 'Médecine générale' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Type de consultation:</span>
            <span class="info-value">{{ $consultation['type_consultation'] ?? 'Consultation générale' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Durée:</span>
            <span class="info-value">{{ $consultation['duree'] ?? '30' }} minutes</span>
        </div>
    </div>

    <!-- Motif de consultation -->
    @if(isset($consultation['motif']) && $consultation['motif'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">MOTIF DE CONSULTATION</h3>
        <div class="highlight-box">
            <p>{{ $consultation['motif'] }}</p>
        </div>
    </div>
    @endif

    <!-- Histoire de la maladie -->
    @if(isset($consultation['histoire_maladie']) && $consultation['histoire_maladie'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">HISTOIRE DE LA MALADIE</h3>
        <div class="highlight-box">
            <p>{{ $consultation['histoire_maladie'] }}</p>
        </div>
    </div>
    @endif

    <!-- Examen clinique -->
    @if(isset($consultation['examen_clinique']) && $consultation['examen_clinique'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">EXAMEN CLINIQUE</h3>
        <div class="highlight-box">
            <p>{{ $consultation['examen_clinique'] }}</p>
        </div>
    </div>
    @endif

    <!-- Signes vitaux -->
    @if(isset($consultation['signes_vitaux']) && count($consultation['signes_vitaux']) > 0)
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">SIGNES VITAUX</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Paramètre</th>
                    <th>Valeur</th>
                    <th>Unité</th>
                    <th>Valeurs normales</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultation['signes_vitaux'] as $signe)
                <tr>
                    <td class="font-bold">{{ $signe['parametre'] }}</td>
                    <td>{{ $signe['valeur'] }}</td>
                    <td>{{ $signe['unite'] }}</td>
                    <td>{{ $signe['valeurs_normales'] }}</td>
                    <td>
                        <span class="@if($signe['statut'] == 'Normal') text-success @elseif($signe['statut'] == 'Élevé') text-danger @else text-warning @endif">
                            {{ $signe['statut'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Diagnostic -->
    @if(isset($consultation['diagnostic']) && $consultation['diagnostic'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">DIAGNOSTIC</h3>
        <div class="highlight-box">
            <h4 class="font-bold mb-2">Diagnostic principal:</h4>
            <p>{{ $consultation['diagnostic']['principal'] }}</p>
            
            @if(isset($consultation['diagnostic']['secondaires']) && count($consultation['diagnostic']['secondaires']) > 0)
            <h4 class="font-bold mb-2 mt-3">Diagnostics secondaires:</h4>
            <ul style="list-style-type: disc; padding-left: 20px;">
                @foreach($consultation['diagnostic']['secondaires'] as $diagnostic)
                <li>{{ $diagnostic }}</li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
    @endif

    <!-- Examens complémentaires demandés -->
    @if(isset($consultation['examens_complementaires']) && count($consultation['examens_complementaires']) > 0)
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">EXAMENS COMPLÉMENTAIRES DEMANDÉS</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type d'examen</th>
                    <th>Indication</th>
                    <th>Urgence</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultation['examens_complementaires'] as $examen)
                <tr>
                    <td class="font-bold">{{ $examen['type'] }}</td>
                    <td>{{ $examen['indication'] }}</td>
                    <td>
                        <span class="@if($examen['urgence'] == 'Urgent') text-danger @elseif($examen['urgence'] == 'Semi-urgent') text-warning @else text-info @endif">
                            {{ $examen['urgence'] }}
                        </span>
                    </td>
                    <td>{{ $examen['instructions'] ?? 'Aucune instruction spécifique' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Traitement prescrit -->
    @if(isset($consultation['traitement']) && $consultation['traitement'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">TRAITEMENT PRESCRIT</h3>
        <div class="highlight-box">
            <p>{{ $consultation['traitement'] }}</p>
        </div>
    </div>
    @endif

    <!-- Recommandations et conseils -->
    @if(isset($consultation['recommandations']) && $consultation['recommandations'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">RECOMMANDATIONS ET CONSEILS</h3>
        <div class="warning-box">
            <p>{{ $consultation['recommandations'] }}</p>
        </div>
    </div>
    @endif

    <!-- Pronostic -->
    @if(isset($consultation['pronostic']) && $consultation['pronostic'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">PRONOSTIC</h3>
        <div class="highlight-box">
            <p>{{ $consultation['pronostic'] }}</p>
        </div>
    </div>
    @endif

    <!-- Prochaine consultation -->
    @if(isset($consultation['prochaine_consultation']) && $consultation['prochaine_consultation'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">SUIVI MÉDICAL</h3>
        <div class="highlight-box">
            <div class="info-row">
                <span class="info-label">Prochaine consultation:</span>
                <span class="info-value font-bold">{{ \Carbon\Carbon::parse($consultation['prochaine_consultation'])->format('d/m/Y') }}</span>
            </div>
            @if(isset($consultation['motif_suivi']) && $consultation['motif_suivi'])
            <div class="info-row">
                <span class="info-label">Motif du suivi:</span>
                <span class="info-value">{{ $consultation['motif_suivi'] }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Notes du médecin -->
    @if(isset($consultation['notes_medecin']) && $consultation['notes_medecin'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #3b82f6;">NOTES DU MÉDECIN</h3>
        <div class="highlight-box">
            <p style="font-style: italic;">{{ $consultation['notes_medecin'] }}</p>
        </div>
    </div>
    @endif

    <!-- Résumé de consultation -->
    <div class="warning-box mt-4">
        <h4 class="font-bold mb-2">📋 RÉSUMÉ DE CONSULTATION</h4>
        <p><strong>Patient:</strong> {{ $consultation['patient']['nom'] }} {{ $consultation['patient']['prenom'] }} ({{ \Carbon\Carbon::parse($consultation['patient']['date_naissance'])->age }} ans)</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($consultation['date_consultation'])->format('d/m/Y à H:i') }}</p>
        <p><strong>Médecin:</strong> Dr. {{ $consultation['medecin']['name'] }}</p>
        @if(isset($consultation['diagnostic']['principal']))
        <p><strong>Diagnostic principal:</strong> {{ $consultation['diagnostic']['principal'] }}</p>
        @endif
        <p><strong>Statut:</strong> {{ $consultation['statut'] ?? 'Consultation terminée' }}</p>
    </div>

    <!-- Signature du médecin -->
    <div class="content-section mt-4" style="text-align: right;">
        <div style="margin-top: 30px;">
            <p><strong>Signature et cachet du médecin</strong></p>
            <div style="height: 80px; border: 1px solid #ccc; width: 250px; margin-left: auto; margin-top: 10px; padding: 5px;">
                <div style="font-size: 8px; color: #999;">Espace réservé à la signature et au cachet</div>
            </div>
            <p class="text-sm mt-2">Dr. {{ $consultation['medecin']['name'] }}</p>
            <p class="text-sm">{{ $consultation['medecin']['specialite'] ?? 'Médecin' }}</p>
            <p class="text-sm">N° Ordre: {{ $consultation['medecin']['matricule'] ?? 'Non renseigné' }}</p>
        </div>
    </div>
</div>

@push('pdf-styles')
<style>
    /* Styles spécifiques à la consultation */
    .document-type-consultation .data-table th {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }
    
    .document-type-consultation .highlight-box {
        border-left-color: #3b82f6;
        background: linear-gradient(135deg, #3b82f610, #1d4ed805);
    }
    
    /* Statuts des signes vitaux */
    .vital-sign-normal {
        color: #10b981;
        font-weight: bold;
    }
    
    .vital-sign-warning {
        color: #f59e0b;
        font-weight: bold;
    }
    
    .vital-sign-danger {
        color: #ef4444;
        font-weight: bold;
    }
    
    /* Diagnostic principal plus visible */
    .diagnostic-principal {
        background: linear-gradient(135deg, #3b82f610, #1d4ed805);
        padding: 15px;
        border-left: 4px solid #3b82f6;
        border-radius: 0 8px 8px 0;
        font-weight: bold;
        color: #1d4ed8;
    }
    
    /* Examens urgents */
    .examen-urgent {
        background-color: #fef2f2;
        color: #dc2626;
    }
    
    .examen-semi-urgent {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    /* Style pour les listes */
    .consultation-list {
        list-style: none;
        padding-left: 0;
    }
    
    .consultation-list li {
        padding: 5px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .consultation-list li:before {
        content: "→ ";
        color: #3b82f6;
        font-weight: bold;
    }
</style>
@endpush
@endsection
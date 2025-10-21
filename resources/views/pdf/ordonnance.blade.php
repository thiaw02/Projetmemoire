@extends('pdf.layout.base')

@section('content')
<div class="content-section">
    <!-- Informations Patient -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #dc2626;">INFORMATIONS PATIENT</h3>
        <div class="info-row">
            <span class="info-label">Nom complet:</span>
            <span class="info-value font-bold">{{ $ordonnance['patient']['nom'] }} {{ $ordonnance['patient']['prenom'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de naissance:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($ordonnance['patient']['date_naissance'])->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Âge:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($ordonnance['patient']['date_naissance'])->age }} ans</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sexe:</span>
            <span class="info-value">{{ $ordonnance['patient']['sexe'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Téléphone:</span>
            <span class="info-value">{{ $ordonnance['patient']['telephone'] ?? 'Non renseigné' }}</span>
        </div>
    </div>

    <!-- Informations Médecin -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #dc2626;">MÉDECIN PRESCRIPTEUR</h3>
        <div class="info-row">
            <span class="info-label">Docteur:</span>
            <span class="info-value font-bold">Dr. {{ $ordonnance['medecin']['name'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Spécialité:</span>
            <span class="info-value">{{ $ordonnance['medecin']['specialite'] ?? 'Médecine générale' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Numéro d'ordre:</span>
            <span class="info-value">{{ $ordonnance['medecin']['matricule'] ?? 'Non renseigné' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cabinet:</span>
            <span class="info-value">{{ $ordonnance['medecin']['cabinet'] ?? 'Non renseigné' }}</span>
        </div>
    </div>

    <!-- Date de consultation -->
    <div class="info-row mb-4">
        <span class="info-label">Date de consultation:</span>
        <span class="info-value font-bold">{{ \Carbon\Carbon::parse($ordonnance['date_consultation'])->format('d/m/Y') }}</span>
    </div>

    <!-- Diagnostic -->
    @if(isset($ordonnance['diagnostic']) && $ordonnance['diagnostic'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #dc2626;">DIAGNOSTIC</h3>
        <div class="highlight-box">
            <p>{{ $ordonnance['diagnostic'] }}</p>
        </div>
    </div>
    @endif

    <!-- Médicaments prescrits -->
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #dc2626;">PRESCRIPTION MÉDICALE</h3>
        
        @if(isset($ordonnance['medicaments']) && count($ordonnance['medicaments']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Médicament</th>
                    <th>Dosage</th>
                    <th>Fréquence</th>
                    <th>Durée</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordonnance['medicaments'] as $medicament)
                <tr>
                    <td class="font-bold">{{ $medicament['nom'] }}</td>
                    <td>{{ $medicament['dosage'] }}</td>
                    <td>{{ $medicament['frequence'] }}</td>
                    <td>{{ $medicament['duree'] }}</td>
                    <td>{{ $medicament['instructions'] ?? 'Suivre les indications du médecin' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="warning-box">
            <p>Aucun médicament n'a été prescrit dans cette ordonnance.</p>
        </div>
        @endif
    </div>

    <!-- Instructions générales -->
    @if(isset($ordonnance['instructions_generales']) && $ordonnance['instructions_generales'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #dc2626;">INSTRUCTIONS GÉNÉRALES</h3>
        <div class="highlight-box">
            <p>{{ $ordonnance['instructions_generales'] }}</p>
        </div>
    </div>
    @endif

    <!-- Recommandations -->
    @if(isset($ordonnance['recommandations']) && $ordonnance['recommandations'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #dc2626;">RECOMMANDATIONS</h3>
        <div class="warning-box">
            <p>{{ $ordonnance['recommandations'] }}</p>
        </div>
    </div>
    @endif

    <!-- Prochaine consultation -->
    @if(isset($ordonnance['prochaine_consultation']) && $ordonnance['prochaine_consultation'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #dc2626;">PROCHAINE CONSULTATION</h3>
        <div class="highlight-box">
            <div class="info-row">
                <span class="info-label">Date recommandée:</span>
                <span class="info-value font-bold">{{ \Carbon\Carbon::parse($ordonnance['prochaine_consultation'])->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Avertissements médicaux -->
    <div class="alert-box mt-4">
        <h4 class="font-bold mb-2">⚠️ AVERTISSEMENTS IMPORTANTS</h4>
        <ul style="list-style-type: disc; padding-left: 20px;">
            <li>Respecter scrupuleusement les dosages et la durée du traitement</li>
            <li>Ne pas interrompre le traitement sans avis médical</li>
            <li>En cas d'effets secondaires, consulter immédiatement votre médecin</li>
            <li>Conserver les médicaments dans un endroit sec et à l'abri de la lumière</li>
            <li>Ne pas partager vos médicaments avec d'autres personnes</li>
            <li>Cette ordonnance est valable 3 mois à partir de la date d'émission</li>
        </ul>
    </div>

    <!-- Signature du médecin -->
    <div class="content-section mt-4" style="text-align: right;">
        <div style="margin-top: 30px;">
            <p><strong>Signature du médecin</strong></p>
            <div style="height: 60px; border-bottom: 1px solid #ccc; width: 200px; margin-left: auto; margin-top: 10px;"></div>
            <p class="text-sm mt-2">Dr. {{ $ordonnance['medecin']['name'] }}</p>
            <p class="text-sm">{{ $ordonnance['medecin']['specialite'] ?? 'Médecin' }}</p>
        </div>
    </div>
</div>

@push('pdf-styles')
<style>
    /* Styles spécifiques à l'ordonnance */
    .document-type-ordonnance .data-table th {
        background: linear-gradient(135deg, #dc2626, #991b1b);
    }
    
    .document-type-ordonnance .highlight-box {
        border-left-color: #dc2626;
        background: linear-gradient(135deg, #dc262610, #991b1b05);
    }
    
    .medicament-row {
        padding: 10px;
        margin-bottom: 8px;
        border-left: 3px solid #dc2626;
        background: #fef2f2;
    }
    
    .medicament-name {
        font-weight: bold;
        color: #991b1b;
        font-size: 12px;
    }
    
    .medicament-details {
        font-size: 10px;
        color: #666;
        margin-top: 3px;
    }
    
    /* Numérotation des médicaments */
    .data-table tbody tr {
        counter-increment: medicament-counter;
    }
    
    .data-table tbody tr td:first-child::before {
        content: counter(medicament-counter) ". ";
        font-weight: bold;
        color: #dc2626;
    }
    
    .data-table tbody {
        counter-reset: medicament-counter;
    }
</style>
@endpush
@endsection
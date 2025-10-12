@extends('pdf.layout.base', ['documentType' => 'Certificat Médical', 'accentColor' => '#28a745'])

@section('content')
<div class="document-content">
    <!-- En-tête du certificat -->
    <div class="section-header" style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #28a745; font-size: 24px; margin-bottom: 10px;">CERTIFICAT MÉDICAL</h1>
        <p style="font-size: 14px; color: #666; margin: 5px 0;">N° {{ $certificat['numero'] ?? 'CERT-' . date('Y-m-d-H-i-s') }}</p>
        <p style="font-size: 12px; color: #888;">Établi le {{ \Carbon\Carbon::parse($certificat['date_etablissement'] ?? now())->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- Informations du médecin -->
    <div class="info-section" style="margin-bottom: 25px;">
        <div class="info-box" style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin-bottom: 20px;">
            <h3 style="color: #28a745; margin-bottom: 10px; font-size: 16px;">Médecin Certifiant</h3>
            <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <strong>Dr. {{ $certificat['medecin']['nom'] ?? '' }}</strong><br>
                    {{ $certificat['medecin']['specialite'] ?? 'Médecine générale' }}<br>
                    {{ $certificat['medecin']['diplomes'] ?? '' }}
                </div>
                <div>
                    <strong>N° Ordre:</strong> {{ $certificat['medecin']['numero_ordre'] ?? '' }}<br>
                    <strong>Tél:</strong> {{ $certificat['medecin']['telephone'] ?? '' }}<br>
                    <strong>Email:</strong> {{ $certificat['medecin']['email'] ?? '' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Informations du patient -->
    <div class="info-section" style="margin-bottom: 25px;">
        <div class="info-box" style="background-color: #fff; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px;">
            <h3 style="color: #495057; margin-bottom: 15px; font-size: 16px;">Informations du Patient</h3>
            <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <p><strong>Nom et Prénom:</strong><br>
                    {{ $certificat['patient']['nom'] ?? '' }} {{ $certificat['patient']['prenom'] ?? '' }}</p>
                    
                    <p><strong>Date de naissance:</strong><br>
                    {{ $certificat['patient']['date_naissance'] ? \Carbon\Carbon::parse($certificat['patient']['date_naissance'])->format('d/m/Y') : '' }}</p>
                    
                    <p><strong>Sexe:</strong> {{ $certificat['patient']['sexe'] ?? '' }}</p>
                </div>
                <div>
                    <p><strong>Adresse:</strong><br>
                    {{ $certificat['patient']['adresse'] ?? '' }}</p>
                    
                    <p><strong>Téléphone:</strong><br>
                    {{ $certificat['patient']['telephone'] ?? '' }}</p>
                    
                    <p><strong>N° Dossier:</strong> {{ $certificat['patient']['numero_dossier'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu du certificat -->
    <div class="certification-content" style="margin: 30px 0; padding: 25px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #28a745;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="color: #28a745; margin-bottom: 15px; font-size: 18px; text-transform: uppercase;">
                Je soussigné, certifie que :
            </h3>
        </div>

        <div style="font-size: 14px; line-height: 1.6; text-align: justify; margin-bottom: 20px;">
            <p style="margin-bottom: 15px;">
                <strong>{{ $certificat['patient']['nom'] ?? '' }} {{ $certificat['patient']['prenom'] ?? '' }}</strong>, 
                né(e) le {{ $certificat['patient']['date_naissance'] ? \Carbon\Carbon::parse($certificat['patient']['date_naissance'])->format('d/m/Y') : '' }},
            </p>
            
            <!-- Type de certificat -->
            @switch($certificat['type_certificat'] ?? 'medical')
                @case('arret_travail')
                    <p><strong>NÉCESSITE UN ARRÊT DE TRAVAIL</strong></p>
                    <p>Du {{ \Carbon\Carbon::parse($certificat['date_debut'])->format('d/m/Y') }} 
                       au {{ \Carbon\Carbon::parse($certificat['date_fin'])->format('d/m/Y') }} inclus.</p>
                    <p><strong>Durée:</strong> {{ $certificat['duree_arret'] ?? '' }} jour(s)</p>
                    @if($certificat['sortie_autorisee'] ?? false)
                        <p style="color: #28a745;"><strong>✓ Sorties autorisées</strong></p>
                    @else
                        <p style="color: #dc3545;"><strong>✗ Sorties non autorisées</strong></p>
                    @endif
                    @break
                
                @case('aptitude_sport')
                    @if($certificat['apte_sport'] ?? true)
                        <p><strong>EST APTE À LA PRATIQUE SPORTIVE</strong></p>
                        <p>Sport(s) concerné(s): {{ $certificat['sports'] ?? 'Tous sports' }}</p>
                        <p>Niveau: {{ $certificat['niveau_sport'] ?? 'Loisir' }}</p>
                    @else
                        <p><strong>EST INAPTE À LA PRATIQUE SPORTIVE</strong></p>
                        <p>Durée d'inaptitude: {{ $certificat['duree_inaptitude'] ?? '' }}</p>
                    @endif
                    @break
                
                @case('grossesse')
                    <p><strong>EST ENCEINTE</strong></p>
                    <p>Date présumée d'accouchement: {{ $certificat['date_accouchement'] ? \Carbon\Carbon::parse($certificat['date_accouchement'])->format('d/m/Y') : '' }}</p>
                    <p>Terme estimé: {{ $certificat['terme_grossesse'] ?? '' }} semaines</p>
                    @break
                
                @case('hospitalisation')
                    <p><strong>A ÉTÉ HOSPITALISÉ(E)</strong></p>
                    <p>Du {{ \Carbon\Carbon::parse($certificat['date_debut'])->format('d/m/Y') }} 
                       au {{ \Carbon\Carbon::parse($certificat['date_fin'])->format('d/m/Y') }}</p>
                    <p>Service: {{ $certificat['service'] ?? '' }}</p>
                    @break
                
                @case('vaccination')
                    <p><strong>A REÇU LES VACCINATIONS SUIVANTES :</strong></p>
                    @if(isset($certificat['vaccinations']) && is_array($certificat['vaccinations']))
                        <ul style="margin: 10px 0; padding-left: 20px;">
                            @foreach($certificat['vaccinations'] as $vaccin)
                                <li>{{ $vaccin['nom'] ?? '' }} - {{ $vaccin['date'] ? \Carbon\Carbon::parse($vaccin['date'])->format('d/m/Y') : '' }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @break
                
                @case('contre_indication')
                    <p><strong>PRÉSENTE UNE CONTRE-INDICATION À :</strong></p>
                    <p>{{ $certificat['contre_indication'] ?? '' }}</p>
                    <p>Durée: {{ $certificat['duree_contre_indication'] ?? '' }}</p>
                    @break
                
                @default
                    <p><strong>PRÉSENTE L'ÉTAT DE SANTÉ SUIVANT :</strong></p>
                    <p>{{ $certificat['diagnostic'] ?? '' }}</p>
            @endswitch
        </div>
        
        <!-- Observations médicales -->
        @if($certificat['observations'] ?? '')
            <div style="margin-top: 20px; padding: 15px; background-color: #fff; border-radius: 5px; border-left: 3px solid #28a745;">
                <h4 style="color: #28a745; margin-bottom: 10px; font-size: 14px;">Observations médicales :</h4>
                <p style="font-size: 13px; line-height: 1.5;">{{ $certificat['observations'] }}</p>
            </div>
        @endif
        
        <!-- Recommandations -->
        @if($certificat['recommandations'] ?? '')
            <div style="margin-top: 15px; padding: 15px; background-color: #fff3cd; border-radius: 5px; border-left: 3px solid #ffc107;">
                <h4 style="color: #856404; margin-bottom: 10px; font-size: 14px;">Recommandations :</h4>
                <p style="font-size: 13px; line-height: 1.5; color: #856404;">{{ $certificat['recommandations'] }}</p>
            </div>
        @endif
    </div>

    <!-- Informations légales -->
    <div class="legal-info" style="margin: 25px 0; padding: 15px; background-color: #e9ecef; border-radius: 5px; font-size: 12px; color: #6c757d;">
        <h4 style="color: #495057; margin-bottom: 10px; font-size: 13px;">Informations légales :</h4>
        <ul style="margin: 0; padding-left: 20px; line-height: 1.4;">
            <li>Ce certificat est établi à la demande du patient concerné</li>
            <li>Il ne peut être utilisé à d'autres fins que celles pour lesquelles il a été demandé</li>
            <li>Toute falsification de ce document est passible de sanctions pénales</li>
            <li>Validité du certificat: {{ $certificat['validite'] ?? '3 mois' }} à compter de sa date d'établissement</li>
        </ul>
        @if($certificat['type_certificat'] === 'arret_travail')
            <p style="margin-top: 10px; color: #dc3545; font-weight: bold;">
                ⚠️ Ce certificat doit être transmis à l'employeur dans les 48h (hors week-end et jours fériés)
            </p>
        @endif
    </div>

    <!-- Signature -->
    <div class="signature-section" style="margin-top: 40px; display: flex; justify-content: flex-end;">
        <div style="text-align: center; min-width: 200px;">
            <p style="margin-bottom: 50px; font-size: 14px; color: #495057;">
                <strong>Fait à {{ $certificat['lieu'] ?? 'Dakar' }}, le {{ \Carbon\Carbon::parse($certificat['date_etablissement'] ?? now())->format('d/m/Y') }}</strong>
            </p>
            
            @if($certificat['signature_numerique'] ?? false)
                <div style="margin-bottom: 20px; padding: 10px; border: 1px solid #28a745; border-radius: 5px; background-color: #d4edda;">
                    <p style="color: #28a745; font-size: 12px; margin: 0;">
                        ✓ Document signé numériquement
                    </p>
                </div>
            @else
                <div style="height: 60px; margin-bottom: 10px; border-bottom: 1px solid #dee2e6;"></div>
            @endif
            
            <p style="font-size: 14px; color: #495057; margin: 0;">
                <strong>Dr. {{ $certificat['medecin']['nom'] ?? '' }}</strong><br>
                {{ $certificat['medecin']['specialite'] ?? 'Médecine générale' }}<br>
                <span style="font-size: 12px;">Ordre des Médecins N° {{ $certificat['medecin']['numero_ordre'] ?? '' }}</span>
            </p>
        </div>
    </div>

    <!-- QR Code et références (optionnel) -->
    @if($certificat['qr_code'] ?? false)
        <div class="qr-section" style="margin-top: 30px; text-align: center; border-top: 1px solid #dee2e6; padding-top: 15px;">
            <p style="font-size: 11px; color: #6c757d; margin-bottom: 10px;">
                QR Code de vérification - {{ $certificat['numero'] ?? '' }}
            </p>
            <!-- Ici vous pourriez ajouter la génération du QR Code -->
            <div style="width: 80px; height: 80px; border: 1px solid #dee2e6; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #adb5bd;">
                QR Code
            </div>
        </div>
    @endif
</div>

<style>
    .document-content {
        font-family: 'DejaVu Sans', sans-serif;
        line-height: 1.4;
        color: #212529;
    }
    
    .info-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 15px !important;
    }
    
    .info-grid p {
        margin: 0 0 10px 0;
        font-size: 13px;
    }
    
    .certification-content p {
        margin-bottom: 12px;
    }
    
    .signature-section {
        page-break-inside: avoid;
    }
    
    @media print {
        .document-content {
            margin: 0;
            padding: 0;
        }
    }
</style>
@endsection
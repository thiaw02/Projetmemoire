@extends('pdf.layout.base', ['documentType' => 'Planning Médical', 'accentColor' => '#6f42c1'])

@section('content')
<div class="document-content">
    <!-- En-tête du planning -->
    <div class="section-header" style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #6f42c1; font-size: 22px; margin-bottom: 10px;">{{ $planning['title'] ?? 'PLANNING MÉDICAL' }}</h1>
        <div style="background-color: #f8f9ff; padding: 15px; border-radius: 8px; border-left: 4px solid #6f42c1;">
            <p style="font-size: 14px; margin: 5px 0; color: #495057;">
                <strong>Période :</strong> 
                Du {{ \Carbon\Carbon::parse($planning['periode']['debut'])->format('d/m/Y') }} 
                au {{ \Carbon\Carbon::parse($planning['periode']['fin'])->format('d/m/Y') }}
            </p>
            <p style="font-size: 12px; color: #6c757d; margin: 5px 0;">
                Généré le {{ \Carbon\Carbon::parse($planning['date'])->format('d/m/Y à H:i') }}
            </p>
        </div>
    </div>

    <!-- Statistiques générales -->
    @if(isset($planning['statistiques']))
    <div class="stats-section" style="margin-bottom: 30px;">
        <h3 style="color: #6f42c1; margin-bottom: 15px; font-size: 16px; border-bottom: 2px solid #6f42c1; padding-bottom: 5px;">
            📊 Statistiques Générales
        </h3>
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
            <div class="stat-card" style="background-color: #d4edda; padding: 15px; border-radius: 8px; text-align: center; border-left: 4px solid #28a745;">
                <div style="font-size: 24px; font-weight: bold; color: #28a745; margin-bottom: 5px;">
                    {{ $planning['statistiques']['total'] ?? 0 }}
                </div>
                <div style="font-size: 12px; color: #155724;">Total RDV</div>
            </div>
            
            <div class="stat-card" style="background-color: #d1ecf1; padding: 15px; border-radius: 8px; text-align: center; border-left: 4px solid #17a2b8;">
                <div style="font-size: 24px; font-weight: bold; color: #17a2b8; margin-bottom: 5px;">
                    {{ $planning['statistiques']['confirmes'] ?? 0 }}
                </div>
                <div style="font-size: 12px; color: #0c5460;">Confirmés</div>
            </div>
            
            <div class="stat-card" style="background-color: #fff3cd; padding: 15px; border-radius: 8px; text-align: center; border-left: 4px solid #ffc107;">
                <div style="font-size: 24px; font-weight: bold; color: #856404; margin-bottom: 5px;">
                    {{ $planning['statistiques']['en_attente'] ?? 0 }}
                </div>
                <div style="font-size: 12px; color: #856404;">En attente</div>
            </div>
            
            <div class="stat-card" style="background-color: #f8d7da; padding: 15px; border-radius: 8px; text-align: center; border-left: 4px solid #dc3545;">
                <div style="font-size: 24px; font-weight: bold; color: #dc3545; margin-bottom: 5px;">
                    {{ $planning['statistiques']['annules'] ?? 0 }}
                </div>
                <div style="font-size: 12px; color: #721c24;">Annulés</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Liste des médecins -->
    @if(isset($planning['medecins']) && count($planning['medecins']) > 0)
    <div class="medecins-section" style="margin-bottom: 25px;">
        <h3 style="color: #6f42c1; margin-bottom: 15px; font-size: 16px; border-bottom: 2px solid #6f42c1; padding-bottom: 5px;">
            👨‍⚕️ Médecins Concernés
        </h3>
        <div class="medecins-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            @foreach($planning['medecins']->take(6) as $medecin)
                <div style="background-color: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 3px solid #6f42c1;">
                    <p style="margin: 0; font-weight: bold; color: #495057;">
                        Dr. {{ $medecin['name'] ?? $medecin['nom'] ?? '' }}
                    </p>
                    <p style="margin: 2px 0; font-size: 12px; color: #6c757d;">
                        {{ $medecin['specialite'] ?? 'Médecine générale' }}
                    </p>
                </div>
            @endforeach
        </div>
        @if(count($planning['medecins']) > 6)
            <p style="text-align: center; margin-top: 10px; font-size: 12px; color: #6c757d; font-style: italic;">
                ... et {{ count($planning['medecins']) - 6 }} autres médecins
            </p>
        @endif
    </div>
    @endif

    <!-- Planning détaillé -->
    <div class="planning-section" style="margin-bottom: 30px;">
        <h3 style="color: #6f42c1; margin-bottom: 15px; font-size: 16px; border-bottom: 2px solid #6f42c1; padding-bottom: 5px;">
            📅 Planning Détaillé
        </h3>
        
        @if(isset($planning['rendezvous']) && count($planning['rendezvous']) > 0)
            @php
                $rdvParJour = collect($planning['rendezvous'])->groupBy(function($rdv) {
                    return \Carbon\Carbon::parse($rdv['date'])->format('Y-m-d');
                });
            @endphp
            
            @foreach($rdvParJour as $date => $rdvDuJour)
                <div class="jour-section" style="margin-bottom: 25px; page-break-inside: avoid;">
                    <div class="jour-header" style="background-color: #6f42c1; color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; margin-bottom: 0;">
                        <h4 style="margin: 0; font-size: 14px; display: flex; justify-content: space-between;">
                            <span>
                                📅 {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </span>
                            <span style="font-size: 12px; background-color: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 12px;">
                                {{ count($rdvDuJour) }} RDV
                            </span>
                        </h4>
                    </div>
                    
                    <div class="rdv-table" style="background-color: #fff; border: 1px solid #6f42c1; border-top: none; border-radius: 0 0 8px 8px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                            <thead>
                                <tr style="background-color: #f8f9ff;">
                                    <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold; color: #495057;">Heure</th>
                                    <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold; color: #495057;">Patient</th>
                                    <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold; color: #495057;">Médecin</th>
                                    <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold; color: #495057;">Type</th>
                                    <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold; color: #495057;">Statut</th>
                                    <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold; color: #495057;">Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rdvDuJour->sortBy('heure') as $rdv)
                                    <tr style="border-bottom: 1px solid #e9ecef;">
                                        <td style="padding: 8px; border: 1px solid #e9ecef; text-align: center; font-weight: bold; color: #6f42c1;">
                                            {{ $rdv['heure'] ?? '' }}
                                        </td>
                                        <td style="padding: 8px; border: 1px solid #e9ecef;">
                                            <div style="font-weight: bold; color: #495057;">
                                                {{ ($rdv['patient']['nom'] ?? '') }} {{ ($rdv['patient']['prenom'] ?? '') }}
                                            </div>
                                            @if(isset($rdv['patient']['date_naissance']))
                                                <div style="font-size: 10px; color: #6c757d;">
                                                    {{ \Carbon\Carbon::parse($rdv['patient']['date_naissance'])->age }} ans
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 8px; border: 1px solid #e9ecef;">
                                            <div style="font-weight: bold; color: #495057;">
                                                Dr. {{ $rdv['medecin']['name'] ?? $rdv['medecin']['nom'] ?? '' }}
                                            </div>
                                            @if(isset($rdv['medecin']['specialite']))
                                                <div style="font-size: 10px; color: #6c757d;">
                                                    {{ $rdv['medecin']['specialite'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 8px; border: 1px solid #e9ecef; font-size: 10px;">
                                            {{ $rdv['type_rdv'] ?? 'Consultation' }}
                                            @if($rdv['motif'] ?? '')
                                                <div style="color: #6c757d; margin-top: 2px;">
                                                    {{ Str::limit($rdv['motif'], 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 8px; border: 1px solid #e9ecef; text-align: center;">
                                            @php
                                                $statut = $rdv['statut'] ?? 'En attente';
                                                $couleurStatut = [
                                                    'Confirmé' => ['bg' => '#d4edda', 'color' => '#155724'],
                                                    'En attente' => ['bg' => '#fff3cd', 'color' => '#856404'],
                                                    'Annulé' => ['bg' => '#f8d7da', 'color' => '#721c24'],
                                                    'Terminé' => ['bg' => '#d1ecf1', 'color' => '#0c5460']
                                                ][$statut] ?? ['bg' => '#e9ecef', 'color' => '#495057'];
                                            @endphp
                                            <span style="background-color: {{ $couleurStatut['bg'] }}; color: {{ $couleurStatut['color'] }}; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold;">
                                                {{ $statut }}
                                            </span>
                                        </td>
                                        <td style="padding: 8px; border: 1px solid #e9ecef; font-size: 10px;">
                                            @if($rdv['patient']['telephone'] ?? '')
                                                <div>📱 {{ $rdv['patient']['telephone'] }}</div>
                                            @endif
                                            @if($rdv['patient']['email'] ?? '')
                                                <div style="margin-top: 2px;">📧 {{ Str::limit($rdv['patient']['email'], 25) }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-data" style="text-align: center; padding: 40px; background-color: #f8f9fa; border-radius: 8px;">
                <p style="color: #6c757d; font-size: 16px; margin: 0;">
                    📅 Aucun rendez-vous programmé pour cette période
                </p>
            </div>
        @endif
    </div>

    <!-- Planning par médecin -->
    @if(isset($planning['rendezvous']) && count($planning['rendezvous']) > 0)
    <div class="planning-medecin-section" style="margin-top: 30px; page-break-before: always;">
        <h3 style="color: #6f42c1; margin-bottom: 15px; font-size: 16px; border-bottom: 2px solid #6f42c1; padding-bottom: 5px;">
            👨‍⚕️ Planning par Médecin
        </h3>
        
        @php
            $rdvParMedecin = collect($planning['rendezvous'])->groupBy(function($rdv) {
                return $rdv['medecin']['name'] ?? $rdv['medecin']['nom'] ?? 'Médecin inconnu';
            });
        @endphp
        
        @foreach($rdvParMedecin as $nomMedecin => $rdvMedecin)
            <div class="medecin-planning" style="margin-bottom: 25px; page-break-inside: avoid;">
                <div class="medecin-header" style="background-color: #f8f9ff; padding: 12px 15px; border-radius: 8px; border-left: 4px solid #6f42c1; margin-bottom: 15px;">
                    <h4 style="margin: 0; color: #495057; font-size: 14px;">
                        Dr. {{ $nomMedecin }}
                        <span style="float: right; font-size: 12px; color: #6c757d; background-color: #6f42c1; color: white; padding: 2px 8px; border-radius: 12px;">
                            {{ count($rdvMedecin) }} RDV
                        </span>
                    </h4>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 15px;">
                    <thead>
                        <tr style="background-color: #6f42c1; color: white;">
                            <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold;">Date & Heure</th>
                            <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold;">Patient</th>
                            <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold;">Type/Motif</th>
                            <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold;">Statut</th>
                            <th style="padding: 8px; border: 1px solid #e9ecef; font-weight: bold;">Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rdvMedecin->sortBy(['date', 'heure']) as $rdv)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td style="padding: 8px; border: 1px solid #e9ecef; text-align: center;">
                                    <div style="font-weight: bold; color: #6f42c1;">
                                        {{ \Carbon\Carbon::parse($rdv['date'])->format('d/m/Y') }}
                                    </div>
                                    <div style="font-size: 10px; color: #495057;">
                                        {{ $rdv['heure'] ?? '' }}
                                    </div>
                                </td>
                                <td style="padding: 8px; border: 1px solid #e9ecef;">
                                    <div style="font-weight: bold; color: #495057;">
                                        {{ ($rdv['patient']['nom'] ?? '') }} {{ ($rdv['patient']['prenom'] ?? '') }}
                                    </div>
                                    @if(isset($rdv['patient']['date_naissance']))
                                        <div style="font-size: 10px; color: #6c757d;">
                                            {{ \Carbon\Carbon::parse($rdv['patient']['date_naissance'])->age }} ans
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 8px; border: 1px solid #e9ecef;">
                                    <div style="font-weight: bold; font-size: 10px; color: #495057;">
                                        {{ $rdv['type_rdv'] ?? 'Consultation' }}
                                    </div>
                                    @if($rdv['motif'] ?? '')
                                        <div style="font-size: 10px; color: #6c757d; margin-top: 2px;">
                                            {{ Str::limit($rdv['motif'], 40) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 8px; border: 1px solid #e9ecef; text-align: center;">
                                    @php
                                        $statut = $rdv['statut'] ?? 'En attente';
                                        $couleurStatut = [
                                            'Confirmé' => ['bg' => '#d4edda', 'color' => '#155724'],
                                            'En attente' => ['bg' => '#fff3cd', 'color' => '#856404'],
                                            'Annulé' => ['bg' => '#f8d7da', 'color' => '#721c24'],
                                            'Terminé' => ['bg' => '#d1ecf1', 'color' => '#0c5460']
                                        ][$statut] ?? ['bg' => '#e9ecef', 'color' => '#495057'];
                                    @endphp
                                    <span style="background-color: {{ $couleurStatut['bg'] }}; color: {{ $couleurStatut['color'] }}; padding: 3px 8px; border-radius: 12px; font-size: 9px; font-weight: bold;">
                                        {{ $statut }}
                                    </span>
                                </td>
                                <td style="padding: 8px; border: 1px solid #e9ecef; font-size: 9px;">
                                    @if($rdv['patient']['telephone'] ?? '')
                                        <div>📱 {{ $rdv['patient']['telephone'] }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Notes et légendes -->
    <div class="notes-section" style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #6f42c1;">
        <h4 style="color: #6f42c1; margin-bottom: 10px; font-size: 14px;">📝 Notes et Légendes</h4>
        <div style="font-size: 12px; color: #495057; line-height: 1.5;">
            <p style="margin: 5px 0;"><strong>Statuts :</strong></p>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li><span style="background-color: #d4edda; color: #155724; padding: 2px 6px; border-radius: 8px; font-size: 10px;">Confirmé</span> - Rendez-vous confirmé par le patient</li>
                <li><span style="background-color: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 8px; font-size: 10px;">En attente</span> - En attente de confirmation</li>
                <li><span style="background-color: #f8d7da; color: #721c24; padding: 2px 6px; border-radius: 8px; font-size: 10px;">Annulé</span> - Rendez-vous annulé</li>
                <li><span style="background-color: #d1ecf1; color: #0c5460; padding: 2px 6px; border-radius: 8px; font-size: 10px;">Terminé</span> - Consultation terminée</li>
            </ul>
            <p style="margin-top: 15px; font-size: 11px; color: #6c757d; font-style: italic;">
                ℹ️ Ce planning est généré automatiquement et peut être sujet à des modifications de dernière minute.
                Veuillez vérifier les confirmations avant chaque rendez-vous.
            </p>
        </div>
    </div>
</div>

<style>
    .document-content {
        font-family: 'DejaVu Sans', sans-serif;
        line-height: 1.4;
        color: #212529;
    }
    
    .stats-grid, .medecins-grid {
        display: grid !important;
        gap: 15px !important;
    }
    
    .stats-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
    
    .medecins-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        border: 1px solid #e9ecef;
        padding: 8px;
        vertical-align: top;
    }
    
    .jour-section {
        page-break-inside: avoid;
    }
    
    .medecin-planning {
        page-break-inside: avoid;
    }
    
    @media print {
        .document-content {
            margin: 0;
            padding: 0;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>
@endsection
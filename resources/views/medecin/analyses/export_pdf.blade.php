<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyses Médicales - {{ $medecin->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c5f2d;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2c5f2d;
            font-size: 24px;
            margin: 0;
        }
        
        .header h2 {
            color: #666;
            font-size: 16px;
            margin: 5px 0;
        }
        
        .medecin-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .export-info {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .filtres {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background-color: #f1f3f4;
            padding: 15px;
            border-radius: 5px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2c5f2d;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #2c5f2d;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            font-size: 10px;
        }
        
        .etat-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .etat-programmee {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .etat-en_cours {
            background-color: #ffeaa7;
            color: #6c4e00;
        }
        
        .etat-terminee {
            background-color: #d4edda;
            color: #155724;
        }
        
        .etat-annulee {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .resultats-cell {
            max-width: 200px;
            word-wrap: break-word;
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>🧪 ANALYSES MÉDICALES</h1>
        <h2>Dr {{ $medecin->name }}</h2>
    </div>
    
    <!-- Informations sur le médecin -->
    <div class="medecin-info">
        <strong>Médecin :</strong> Dr {{ $medecin->name }}<br>
        @if($medecin->email)
            <strong>Email :</strong> {{ $medecin->email }}<br>
        @endif
        @if($medecin->pro_phone)
            <strong>Téléphone :</strong> {{ $medecin->pro_phone }}<br>
        @endif
    </div>
    
    <!-- Informations d'export -->
    <div class="export-info">
        Export généré le {{ $dateExport->format('d/m/Y à H:i') }}<br>
        Nombre d'analyses : {{ $analyses->count() }}
    </div>
    
    <!-- Filtres appliqués -->
    @if(!empty($filtres) && array_filter($filtres))
        <div class="filtres">
            <strong>Filtres appliqués :</strong><br>
            @if($filtres['patient_id'] ?? false)
                Patient sélectionné<br>
            @endif
            @if($filtres['type_analyse'] ?? false)
                Type : {{ $filtres['type_analyse'] }}<br>
            @endif
            @if($filtres['date_debut'] ?? false)
                Du {{ \Carbon\Carbon::parse($filtres['date_debut'])->format('d/m/Y') }}<br>
            @endif
            @if($filtres['date_fin'] ?? false)
                Au {{ \Carbon\Carbon::parse($filtres['date_fin'])->format('d/m/Y') }}<br>
            @endif
        </div>
    @endif
    
    <!-- Statistiques rapides -->
    @php
        $stats = [
            'total' => $analyses->count(),
            'programmees' => $analyses->where('etat', 'programmee')->count(),
            'en_cours' => $analyses->where('etat', 'en_cours')->count(),
            'terminees' => $analyses->where('etat', 'terminee')->count(),
            'annulees' => $analyses->where('etat', 'annulee')->count(),
        ];
    @endphp
    
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['programmees'] }}</div>
            <div class="stat-label">Programmées</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['en_cours'] }}</div>
            <div class="stat-label">En cours</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['terminees'] }}</div>
            <div class="stat-label">Terminées</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['annulees'] }}</div>
            <div class="stat-label">Annulées</div>
        </div>
    </div>
    
    <!-- Tableau des analyses -->
    @if($analyses->isEmpty())
        <div class="no-data">
            Aucune analyse trouvée avec les critères sélectionnés.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Date</th>
                    <th style="width: 25%;">Patient</th>
                    <th style="width: 20%;">Type d'analyse</th>
                    <th style="width: 10%;">État</th>
                    <th style="width: 35%;">Résultats</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analyses as $index => $analyse)
                    <tr>
                        <td>
                            @if($analyse->date_analyse)
                                <strong>{{ \Carbon\Carbon::parse($analyse->date_analyse)->format('d/m/Y') }}</strong><br>
                                <small style="color: #666;">{{ $analyse->created_at->format('H:i') }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($analyse->patient)
                                <strong>{{ $analyse->patient->nom }} {{ $analyse->patient->prenom }}</strong><br>
                                @if($analyse->patient->telephone)
                                    <small style="color: #666;">{{ $analyse->patient->telephone }}</small>
                                @endif
                            @else
                                <span style="color: #999;">Patient non trouvé</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $analyse->type_analyse ?: '—' }}</strong>
                        </td>
                        <td>
                            @php
                                $etats = [
                                    'programmee' => ['etat-programmee', '📅 Programmée'],
                                    'en_cours' => ['etat-en_cours', '⏳ En cours'],
                                    'terminee' => ['etat-terminee', '✅ Terminée'],
                                    'annulee' => ['etat-annulee', '❌ Annulée']
                                ];
                                $etatInfo = $etats[$analyse->etat] ?? ['', $analyse->etat ?: '—'];
                            @endphp
                            <span class="etat-badge {{ $etatInfo[0] }}">{{ $etatInfo[1] }}</span>
                        </td>
                        <td class="resultats-cell">
                            @if($analyse->resultats)
                                {{ $analyse->resultats }}
                            @else
                                <span style="color: #999; font-style: italic;">En attente</span>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- Saut de page tous les 25 éléments --}}
                    @if(($index + 1) % 25 === 0 && !$loop->last)
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Date</th>
                                    <th style="width: 25%;">Patient</th>
                                    <th style="width: 20%;">Type d'analyse</th>
                                    <th style="width: 10%;">État</th>
                                    <th style="width: 35%;">Résultats</th>
                                </tr>
                            </thead>
                        <tbody>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
    
    <!-- Pied de page -->
    <div class="footer">
        <strong>SMART-HEALTH</strong> - Système de Gestion Médicale<br>
        Document généré automatiquement le {{ $dateExport->format('d/m/Y à H:i:s') }}<br>
        Dr {{ $medecin->name }} - {{ $analyses->count() }} analyse(s)
    </div>
</body>
</html>
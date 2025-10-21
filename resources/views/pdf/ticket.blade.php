@extends('pdf.layout.base')

@section('content')
<div class="content-section">
    <!-- Informations Patient/Client -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #f59e0b;">INFORMATIONS CLIENT</h3>
        <div class="info-row">
            <span class="info-label">Nom complet:</span>
            <span class="info-value font-bold">{{ $ticket['client']['nom'] }} {{ $ticket['client']['prenom'] }}</span>
        </div>
        @if(isset($ticket['client']['telephone']))
        <div class="info-row">
            <span class="info-label">Téléphone:</span>
            <span class="info-value">{{ $ticket['client']['telephone'] }}</span>
        </div>
        @endif
        @if(isset($ticket['client']['email']))
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $ticket['client']['email'] }}</span>
        </div>
        @endif
        @if(isset($ticket['client']['adresse']))
        <div class="info-row">
            <span class="info-label">Adresse:</span>
            <span class="info-value">{{ $ticket['client']['adresse'] }}</span>
        </div>
        @endif
    </div>

    <!-- Détails de la transaction -->
    <div class="highlight-box mb-4">
        <h3 class="font-bold text-lg mb-3" style="color: #f59e0b;">DÉTAILS DE LA TRANSACTION</h3>
        <div class="info-row">
            <span class="info-label">Date de paiement:</span>
            <span class="info-value font-bold">{{ \Carbon\Carbon::parse($ticket['date_paiement'])->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mode de paiement:</span>
            <span class="info-value">{{ $ticket['mode_paiement'] }}</span>
        </div>
        @if(isset($ticket['reference_transaction']))
        <div class="info-row">
            <span class="info-label">Référence transaction:</span>
            <span class="info-value">{{ $ticket['reference_transaction'] }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Statut:</span>
            <span class="info-value">
                <span class="status-badge @if($ticket['statut'] == 'Payé') status-paye @elseif($ticket['statut'] == 'En attente') status-attente @else status-annule @endif">
                    {{ $ticket['statut'] }}
                </span>
            </span>
        </div>
        @if(isset($ticket['caissier']))
        <div class="info-row">
            <span class="info-label">Caissier:</span>
            <span class="info-value">{{ $ticket['caissier'] }}</span>
        </div>
        @endif
    </div>

    <!-- Services/Articles facturés -->
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #f59e0b;">DÉTAIL DES PRESTATIONS</h3>
        
        @if(isset($ticket['items']) && count($ticket['items']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="40%">Prestation</th>
                    <th width="15%">Quantité</th>
                    <th width="15%">Prix unitaire</th>
                    <th width="15%">Remise</th>
                    <th width="10%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($ticket['items'] as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item['nom'] }}</strong>
                        @if(isset($item['description']))
                        <br><small class="text-muted">{{ $item['description'] }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item['quantite'] }}</td>
                    <td class="text-right">{{ number_format($item['prix_unitaire'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">
                        @if($item['remise'] > 0)
                        {{ number_format($item['remise'], 0, ',', ' ') }} FCFA
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-right font-bold">{{ number_format($item['total'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @php $subtotal += $item['total']; @endphp
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Calculs totaux -->
    <div class="content-section mt-4">
        <div style="float: right; width: 300px;">
            <table class="total-table">
                <tr>
                    <td class="label-total">Sous-total:</td>
                    <td class="value-total">{{ number_format($ticket['sous_total'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @if(isset($ticket['remise_globale']) && $ticket['remise_globale'] > 0)
                <tr>
                    <td class="label-total">Remise globale:</td>
                    <td class="value-total text-danger">-{{ number_format($ticket['remise_globale'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
                @if(isset($ticket['taxe']) && $ticket['taxe'] > 0)
                <tr>
                    <td class="label-total">TVA (18%):</td>
                    <td class="value-total">{{ number_format($ticket['taxe'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
                <tr class="total-final">
                    <td class="label-total"><strong>MONTANT TOTAL:</strong></td>
                    <td class="value-total"><strong>{{ number_format($ticket['montant_total'], 0, ',', ' ') }} FCFA</strong></td>
                </tr>
                <tr>
                    <td class="label-total">Montant payé:</td>
                    <td class="value-total">{{ number_format($ticket['montant_paye'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @if(isset($ticket['monnaie']) && $ticket['monnaie'] > 0)
                <tr>
                    <td class="label-total">Monnaie rendue:</td>
                    <td class="value-total">{{ number_format($ticket['monnaie'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- Montant en toutes lettres -->
    <div class="highlight-box mt-4">
        <h4 class="font-bold mb-2">Montant en toutes lettres:</h4>
        <p style="text-transform: uppercase; font-weight: bold;">
            {{ $ticket['montant_lettres'] ?? 'MONTANT NON CONVERTI' }} FRANCS CFA
        </p>
    </div>

    <!-- Informations de paiement spécifiques -->
    @if($ticket['mode_paiement'] == 'Carte bancaire' && isset($ticket['infos_carte']))
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #f59e0b;">INFORMATIONS CARTE BANCAIRE</h3>
        <div class="highlight-box">
            <div class="info-row">
                <span class="info-label">Type de carte:</span>
                <span class="info-value">{{ $ticket['infos_carte']['type'] ?? 'Non spécifié' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Numéro masqué:</span>
                <span class="info-value">**** **** **** {{ $ticket['infos_carte']['derniers_chiffres'] ?? '****' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Code autorisation:</span>
                <span class="info-value">{{ $ticket['infos_carte']['code_autorisation'] ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
    @endif

    @if($ticket['mode_paiement'] == 'Mobile Money' && isset($ticket['infos_mobile']))
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #f59e0b;">INFORMATIONS MOBILE MONEY</h3>
        <div class="highlight-box">
            <div class="info-row">
                <span class="info-label">Opérateur:</span>
                <span class="info-value">{{ $ticket['infos_mobile']['operateur'] ?? 'Non spécifié' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Numéro:</span>
                <span class="info-value">{{ $ticket['infos_mobile']['numero'] ?? 'Non spécifié' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Transaction ID:</span>
                <span class="info-value">{{ $ticket['infos_mobile']['transaction_id'] ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Notes et commentaires -->
    @if(isset($ticket['notes']) && $ticket['notes'])
    <div class="content-section">
        <h3 class="font-bold text-lg mb-3" style="color: #f59e0b;">NOTES</h3>
        <div class="highlight-box">
            <p>{{ $ticket['notes'] }}</p>
        </div>
    </div>
    @endif

    <!-- QR Code pour vérification (optionnel) -->
    @if(isset($ticket['qr_code']))
    <div class="content-section" style="text-align: center;">
        <h4 class="font-bold mb-2">Code de vérification</h4>
        <div style="display: inline-block; padding: 10px; border: 1px solid #ccc; background: white;">
            <img src="{{ $ticket['qr_code'] }}" alt="QR Code" style="width: 100px; height: 100px;">
        </div>
        <p class="text-sm mt-2">Scannez ce code pour vérifier l'authenticité du reçu</p>
    </div>
    @endif

    <!-- Conditions et mentions légales -->
    <div class="alert-box mt-4">
        <h4 class="font-bold mb-2">📋 CONDITIONS ET MENTIONS LÉGALES</h4>
        <ul style="list-style-type: disc; padding-left: 20px; font-size: 10px;">
            <li>Ce reçu fait foi du paiement des prestations mentionnées ci-dessus</li>
            <li>En cas de remboursement, ce document original doit être présenté</li>
            <li>Les réclamations doivent être formulées dans un délai de 48h</li>
            <li>Aucun remboursement ne sera effectué après 30 jours</li>
            @if(isset($ticket['conditions_particulieres']))
            @foreach($ticket['conditions_particulieres'] as $condition)
            <li>{{ $condition }}</li>
            @endforeach
            @endif
        </ul>
    </div>

    <!-- Résumé du paiement -->
    <div class="warning-box mt-4">
        <h4 class="font-bold mb-2">💳 RÉSUMÉ DU PAIEMENT</h4>
        <p><strong>Client:</strong> {{ $ticket['client']['nom'] }} {{ $ticket['client']['prenom'] }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($ticket['date_paiement'])->format('d/m/Y à H:i') }}</p>
        <p><strong>Mode de paiement:</strong> {{ $ticket['mode_paiement'] }}</p>
        <p><strong>Montant total:</strong> {{ number_format($ticket['montant_total'], 0, ',', ' ') }} FCFA</p>
        <p><strong>Statut:</strong> {{ $ticket['statut'] }}</p>
        @if(isset($ticket['prochain_rdv']))
        <p><strong>Prochain RDV:</strong> {{ \Carbon\Carbon::parse($ticket['prochain_rdv'])->format('d/m/Y à H:i') }}</p>
        @endif
    </div>

    <!-- Remerciements -->
    <div class="content-section mt-4" style="text-align: center;">
        <div style="background: linear-gradient(135deg, #f59e0b15, #d9770610); padding: 20px; border-radius: 12px; border: 2px solid #f59e0b;">
            <h4 class="font-bold mb-2" style="color: #d97706;">🙏 MERCI POUR VOTRE CONFIANCE</h4>
            <p>Nous vous remercions d'avoir choisi {{ $platform_name }} pour vos soins de santé.</p>
            <p>Votre satisfaction est notre priorité.</p>
            @if(isset($ticket['message_personnalise']))
            <p style="font-style: italic; margin-top: 10px;">{{ $ticket['message_personnalise'] }}</p>
            @endif
        </div>
    </div>

    <!-- Signature et cachet -->
    @if($ticket['statut'] == 'Payé')
    <div class="content-section mt-4" style="text-align: right;">
        <div style="margin-top: 20px;">
            <p><strong>Signature et cachet de l'établissement</strong></p>
            <div style="height: 60px; border: 1px solid #ccc; width: 200px; margin-left: auto; margin-top: 10px; padding: 5px;">
                <div style="font-size: 8px; color: #999;">Espace réservé au cachet</div>
            </div>
            <p class="text-sm mt-2">{{ $ticket['caissier'] ?? 'Caissier' }}</p>
            <p class="text-sm">{{ \Carbon\Carbon::parse($ticket['date_paiement'])->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
    @endif
</div>

@push('pdf-styles')
<style>
    /* Styles spécifiques aux tickets */
    .document-type-ticket .data-table th {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    
    .document-type-ticket .highlight-box {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #f59e0b10, #d9770605);
    }
    
    /* Statuts de paiement */
    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .status-paye {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }
    
    .status-attente {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    
    .status-annule {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }
    
    /* Table des totaux */
    .total-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    
    .total-table td {
        padding: 6px 10px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .label-total {
        text-align: left;
        color: #4a5568;
    }
    
    .value-total {
        text-align: right;
        font-weight: 500;
    }
    
    .total-final {
        background: linear-gradient(135deg, #f59e0b15, #d9770610);
        border: 2px solid #f59e0b;
        font-size: 12px;
    }
    
    .total-final td {
        padding: 10px;
        color: #d97706;
        font-size: 13px;
    }
    
    /* Numérotation des items */
    .data-table tbody tr td:first-child {
        background: #f59e0b;
        color: white;
        font-weight: bold;
        text-align: center;
    }
    
    /* Mode de paiement spéciaux */
    .paiement-carte {
        background: linear-gradient(135deg, #3b82f615, #1d4ed810);
        border-left: 4px solid #3b82f6;
    }
    
    .paiement-mobile {
        background: linear-gradient(135deg, #10b98115, #05966910);
        border-left: 4px solid #10b981;
    }
    
    .paiement-especes {
        background: linear-gradient(135deg, #f59e0b15, #d9770610);
        border-left: 4px solid #f59e0b;
    }
    
    /* Effet de pastille pour les quantités */
    .quantite-badge {
        background: #f59e0b;
        color: white;
        padding: 2px 8px;
        border-radius: 50%;
        font-size: 10px;
        font-weight: bold;
    }
    
    /* Détails des prestations */
    .prestation-item {
        border-left: 3px solid #f59e0b;
        padding: 8px;
        margin-bottom: 5px;
        background: #fefdf8;
    }
    
    .prestation-name {
        font-weight: bold;
        color: #d97706;
    }
    
    .prestation-desc {
        font-size: 9px;
        color: #6b7280;
        margin-top: 2px;
    }
    
    /* Remises et promotions */
    .remise-item {
        background: #fef2f2;
        color: #991b1b;
        font-style: italic;
    }
    
    /* Watermark pour les copies */
    .copie-watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 48px;
        color: rgba(239, 68, 68, 0.1);
        font-weight: bold;
        z-index: -1;
    }
    
    /* Version mobile du ticket */
    .ticket-mobile {
        max-width: 300px;
        margin: 0 auto;
    }
    
    .ticket-mobile .data-table {
        font-size: 9px;
    }
    
    .ticket-mobile .data-table th,
    .ticket-mobile .data-table td {
        padding: 4px 6px;
    }
</style>
@endpush
@endsection
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quittance de paiement</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 2px solid #ddd;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .header-left {
            display: table-cell;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            vertical-align: top;
            text-align: right;
        }
        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .hospital-address {
            color: #666;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .receipt-number {
            color: #666;
            font-size: 14px;
        }
        .receipt-date {
            font-size: 14px;
        }
        .divider {
            border-top: 2px solid #ddd;
            margin: 20px 0;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .items-table .amount {
            text-align: right;
        }
        .items-table tfoot th,
        .items-table tfoot td {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="header-left">
                <div class="hospital-name">{{ $hospital['name'] }}</div>
                <div class="hospital-address">{{ $hospital['address'] }}</div>
                @if(!empty($hospital['phone']))
                    <div class="hospital-address">Tél: {{ $hospital['phone'] }}</div>
                @endif
            </div>
            <div class="header-right">
                <div class="receipt-number">Quittance #{{ $order->id }}</div>
                <div class="receipt-date">Généré le {{ $generatedAt->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Payeur:</span> {{ $order->user->name ?? '—' }}
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span> {{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i') }}
            </div>
            <div class="info-row">
                <span class="info-label">Prestataire:</span> {{ strtoupper($order->provider ?? '—') }}
            </div>
            <div class="info-row">
                <span class="info-label">Référence:</span> {{ $order->provider_ref ?? '-' }}
            </div>
            <div class="info-row">
                <span class="info-label">Statut:</span> {{ strtoupper($order->status) }}
            </div>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th class="amount">Montant (XOF)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $it)
                    <tr>
                        <td>{{ $it->label }}</td>
                        <td class="amount">{{ number_format($it->amount, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="amount">{{ number_format($order->total_amount, 0, ',', ' ') }}</th>
                </tr>
            </tfoot>
        </table>
        
        <div class="footer">
            Document généré automatiquement. Merci pour votre confiance.
        </div>
    </div>
</body>
</html>

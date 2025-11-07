<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Sandbox - Test Paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sandbox-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .sandbox-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .sandbox-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .sandbox-body {
            padding: 40px;
        }
        .order-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .btn-test {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            margin: 10px 0;
            transition: all 0.3s ease;
        }
        .btn-success-test {
            background: linear-gradient(135deg, #00b894, #00a085);
            border: none;
            color: white;
        }
        .btn-success-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,184,148,0.3);
            color: white;
        }
        .btn-danger-test {
            background: linear-gradient(135deg, #e17055, #d63031);
            border: none;
            color: white;
        }
        .btn-danger-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(214,48,49,0.3);
            color: white;
        }
        .btn-info-test {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            border: none;
            color: white;
        }
        .btn-info-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(116,185,255,0.3);
            color: white;
        }
        .amount-display {
            font-size: 2rem;
            font-weight: bold;
            color: #2d3436;
            margin: 20px 0;
        }
        .provider-badge {
            display: inline-block;
            background: #00b894;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="sandbox-container">
            <div class="sandbox-card">
                <div class="sandbox-header">
                <i class="bi bi-shield-check" style="font-size: 3rem; margin-bottom: 15px;"></i>
                <h2>Mode Sandbox - Test Paiement</h2>
                <p class="mb-0">Simulation de paiement pour le développement</p>
                </div>

                <div class="sandbox-body">
                    <div class="order-info">
                    <h5><i class="bi bi-receipt"></i> Détails de la commande</h5>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <strong>Commande #{{ $order->id }}</strong><br>
                            <small class="text-muted">ID: {{ $order->provider_ref }}</small>
                        </div>
                        <div class="col-6 text-end">
                            <div class="amount-display">{{ number_format($order->total_amount, 0, ',', ' ') }} XOF</div>
                        </div>
                                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <strong>Fournisseur:</strong><br>
                            <span class="provider-badge">{{ ucfirst($order->provider) }}</span>
                                </div>
                        <div class="col-6 text-end">
                            <strong>Statut:</strong><br>
                            <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                        </div>
                        </div>
                    </div>

                <h5 class="mb-3"><i class="bi bi-gear"></i> Actions de test</h5>
                <p class="text-muted mb-4">Sélectionnez une action pour simuler le comportement du paiement :</p>

                <a href="{{ route('payments.success', ['order' => $order->id]) }}" class="btn btn-success-test btn-test">
                    <i class="bi bi-check-circle me-2"></i>Simuler Succès
                </a>

                <a href="{{ route('payments.cancel', ['order' => $order->id]) }}" class="btn btn-danger-test btn-test">
                    <i class="bi bi-x-circle me-2"></i>Simuler Annulation
                </a>

                <a href="{{ route('patient.dashboard') }}" class="btn btn-info-test btn-test">
                    <i class="bi bi-arrow-left me-2"></i>Retour à l'espace patient
                </a>

                <div class="mt-4 p-3 bg-light rounded">
                    <h6><i class="bi bi-info-circle"></i> Information</h6>
                    <p class="mb-0 small text-muted">
                        En mode sandbox, aucun paiement réel n'est effectué. 
                        Cette page vous permet de tester le flux de paiement sans utiliser de vraies transactions.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
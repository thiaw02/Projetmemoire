<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sandbox Paiement - {{ config('app.name', 'Smart Health') }}</title>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* Reset complet */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Variables CSS */
        :root {
            --primary: #6366f1;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
        }

        /* Base HTML */
        html, body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            overflow-x: hidden;
            position: relative;
        }

        /* Particules animées */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(239, 68, 68, 0.1) 0%, transparent 50%);
            z-index: -1;
            animation: particleFloat 20s ease-in-out infinite;
        }

        @keyframes particleFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        /* Container principal */
        .sandbox-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .sandbox-wrapper {
            max-width: 700px;
            width: 100%;
            animation: slideInUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Carte principale */
        .sandbox-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            overflow: hidden;
            position: relative;
            transition: transform 0.3s ease;
        }

        .sandbox-card:hover {
            transform: translateY(-8px);
        }

        /* En-tête */
        .sandbox-header {
            background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 50%, #3730a3 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .sandbox-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: headerFloat 15s ease-in-out infinite;
        }

        @keyframes headerFloat {
            0%, 100% { 
                transform: translateY(0px) rotateZ(0deg);
                opacity: 0.3;
            }
            50% { 
                transform: translateY(-30px) rotateZ(180deg);
                opacity: 0.1;
            }
        }

        .provider-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 2;
        }

        .provider-badge i {
            animation: spin 4s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .sandbox-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .sandbox-title i {
            font-size: 2.25rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .sandbox-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        /* Corps de la carte */
        .sandbox-body {
            padding: 3rem 2rem;
        }

        .order-info h4 {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .items-list {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-details {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .item-label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .item-amount {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--success);
        }

        .total-amount {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            padding: 2rem;
            border-radius: 16px;
            border: 2px solid var(--success);
            margin-bottom: 3rem;
        }

        .total-amount span:first-child {
            font-weight: 700;
            color: #065f46;
            font-size: 1.1rem;
        }

        .total-amount .amount {
            font-size: 2rem;
            font-weight: 900;
            color: var(--success);
        }

        /* Actions */
        .sandbox-actions h5 {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .btn-modern {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 2.5rem 2rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
        }

        .btn-success {
            background: linear-gradient(145deg, var(--success), #059669, #047857);
            color: white;
            box-shadow: 
                0 10px 25px rgba(16, 185, 129, 0.3),
                0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-success:hover {
            background: linear-gradient(145deg, #059669, #047857, #065f46);
            transform: translateY(-10px) scale(1.05);
            box-shadow: 
                0 25px 50px rgba(16, 185, 129, 0.4),
                0 0 30px rgba(16, 185, 129, 0.2);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            color: var(--danger);
            box-shadow: 
                0 10px 25px rgba(239, 68, 68, 0.15),
                0 3px 8px rgba(0, 0, 0, 0.05),
                0 0 0 2px rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            background: linear-gradient(145deg, var(--danger), #dc2626);
            color: white;
            transform: translateY(-10px) scale(1.05);
            box-shadow: 
                0 25px 50px rgba(239, 68, 68, 0.4),
                0 0 30px rgba(239, 68, 68, 0.2);
        }

        .btn-modern i {
            font-size: 2.5rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            transition: all 0.3s ease;
        }

        .btn-success:hover i {
            animation: successPulse 0.6s ease;
        }

        .btn-danger:hover i {
            animation: shake 0.6s ease;
        }

        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-modern span {
            font-size: 1.2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-modern small {
            opacity: 0.9;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: none;
            letter-spacing: 0;
        }

        /* Info section */
        .sandbox-info {
            display: flex;
            gap: 2rem;
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.8), rgba(219, 234, 254, 0.8));
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 20px;
            border: 2px solid rgba(99, 102, 241, 0.2);
            position: relative;
            overflow: hidden;
        }

        .sandbox-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary), #3730a3);
        }

        .info-icon i {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--primary), #3730a3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .info-content strong {
            color: var(--primary);
            font-size: 1.2rem;
            font-weight: 800;
            display: block;
            margin-bottom: 1rem;
        }

        .info-content p {
            color: #4f46e5;
            font-size: 1rem;
            line-height: 1.7;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sandbox-container {
                padding: 1rem;
            }

            .sandbox-header {
                padding: 2rem 1.5rem;
            }

            .sandbox-title {
                font-size: 2rem;
            }

            .sandbox-body {
                padding: 2rem 1.5rem;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .sandbox-info {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="sandbox-container">
        <div class="sandbox-wrapper">
            <div class="sandbox-card">
                <!-- Header -->
                <div class="sandbox-header">
                    <div class="provider-badge">
                        <i class="bi bi-gear-fill"></i>
                        {{ strtoupper($order->provider ?? 'SANDBOX') }}
                    </div>
                    <h1 class="sandbox-title">
                        <i class="bi bi-credit-card-2-front"></i>
                        Paiement Test
                    </h1>
                    <p class="sandbox-subtitle">Mode simulation - Environnement de développement</p>
                </div>

                <!-- Corps -->
                <div class="sandbox-body">
                    <!-- Informations commande -->
                    <div class="order-info">
                        <h4>
                            <i class="bi bi-receipt"></i>
                            Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </h4>
                        
                        <div class="items-list">
                            @foreach($order->items as $item)
                                <div class="item">
                                    <div class="item-details">
                                        <i class="bi bi-dot"></i>
                                        <span class="item-label">{{ $item->label }}</span>
                                    </div>
                                    <div class="item-amount">{{ number_format($item->amount, 0, ',', ' ') }} XOF</div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="total-amount">
                            <span>Total à payer</span>
                            <span class="amount">{{ number_format($order->total_amount, 0, ',', ' ') }} XOF</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="sandbox-actions">
                        <h5><i class="bi bi-play-circle"></i> Actions de simulation</h5>
                        <div class="action-buttons">
                            <a href="{{ route('payments.success', ['order' => $order->id]) }}" class="btn-modern btn-success">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Simuler succès</span>
                                <small>Paiement confirmé</small>
                            </a>
                            <a href="{{ route('payments.cancel', ['order' => $order->id]) }}" class="btn-modern btn-danger">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Simuler échec</span>
                                <small>Paiement annulé</small>
                            </a>
                        </div>
                    </div>

                    <!-- Informations -->
                    <div class="sandbox-info">
                        <div class="info-icon">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <div class="info-content">
                            <strong>Mode SANDBOX activé</strong>
                            <p>Cette interface simule les paiements pour les tests. Configurez vos clés API Wave/Orange Money dans le fichier .env pour passer en mode production.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation d'apparition séquentielle
            const elements = document.querySelectorAll('.order-info, .sandbox-actions, .sandbox-info');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    el.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 400 + (index * 200));
            });

            // Effet de clic avec loading
            const buttons = document.querySelectorAll('.btn-modern');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const isSuccess = this.classList.contains('btn-success');
                    const originalContent = this.innerHTML;
                    
                    // Animation de loading
                    this.innerHTML = `
                        <i class="bi bi-hourglass-split" style="animation: spin 1s linear infinite;"></i>
                        <span>${isSuccess ? 'Traitement...' : 'Annulation...'}</span>
                    `;
                    
                    this.style.transform = 'scale(0.95)';
                    this.style.opacity = '0.7';
                    this.style.pointerEvents = 'none';
                    
                    // Redirection après animation
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 1500);
                });
            });

            // Effet de particules au survol
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    createParticles(this);
                });
            });

            function createParticles(element) {
                for (let i = 0; i < 8; i++) {
                    setTimeout(() => {
                        const particle = document.createElement('div');
                        particle.style.cssText = `
                            position: fixed;
                            width: 6px;
                            height: 6px;
                            background: rgba(255, 255, 255, 0.8);
                            border-radius: 50%;
                            pointer-events: none;
                            z-index: 1000;
                        `;
                        
                        const rect = element.getBoundingClientRect();
                        particle.style.left = (rect.left + Math.random() * rect.width) + 'px';
                        particle.style.top = (rect.top + Math.random() * rect.height) + 'px';
                        
                        document.body.appendChild(particle);
                        
                        const animation = particle.animate([
                            { transform: 'translateY(0) scale(1)', opacity: 1 },
                            { transform: `translateY(-${60 + Math.random() * 40}px) scale(0)`, opacity: 0 }
                        ], {
                            duration: 1200 + Math.random() * 800,
                            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                        });
                        
                        animation.onfinish = () => particle.remove();
                    }, i * 80);
                }
            }
        });
    </script>
</body>
</html>
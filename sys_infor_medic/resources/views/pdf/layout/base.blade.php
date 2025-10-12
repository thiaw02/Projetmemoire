<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $platform_name }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
            @top-left { content: "{{ $platform_name }}"; }
            @top-right { content: "{{ $document_number }}"; }
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #2d3748;
            background: #ffffff;
        }
        
        /* En-tête avec logo */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 3px solid {{ $primary_color }};
            margin-bottom: 25px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: {{ $primary_color }};
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        .brand-info h1 {
            font-size: 24px;
            font-weight: 900;
            color: {{ $primary_color }};
            margin-bottom: 2px;
            letter-spacing: -0.5px;
        }
        
        .brand-info .tagline {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .document-info {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }
        
        .document-info .doc-number {
            font-size: 12px;
            font-weight: bold;
            color: {{ $secondary_color }};
            margin-bottom: 3px;
        }
        
        .document-info .doc-date {
            margin-bottom: 2px;
        }
        
        /* Titre du document */
        .document-title {
            text-align: center;
            margin: 30px 0;
        }
        
        .document-title h2 {
            font-size: 20px;
            font-weight: 800;
            color: {{ $secondary_color }};
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 30px;
            background: linear-gradient(135deg, {{ $primary_color }}15, {{ $secondary_color }}10);
            border: 2px solid {{ $primary_color }};
            border-radius: 8px;
            display: inline-block;
        }
        
        /* Styles de contenu */
        .content-section {
            margin: 25px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 6px 0;
        }
        
        .info-row:nth-child(even) {
            background-color: #f8fafc;
            padding: 6px 10px;
            border-radius: 4px;
        }
        
        .info-label {
            font-weight: 600;
            color: {{ $secondary_color }};
            width: 30%;
        }
        
        .info-value {
            width: 65%;
            color: #4a5568;
        }
        
        /* Tableaux */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .data-table th {
            background: linear-gradient(135deg, {{ $primary_color }}, {{ $secondary_color }});
            color: white;
            font-weight: 700;
            padding: 12px 15px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        
        .data-table tr:hover {
            background-color: {{ $primary_color }}10;
        }
        
        /* Sections spécialisées */
        .highlight-box {
            background: linear-gradient(135deg, {{ $primary_color }}10, {{ $secondary_color }}05);
            border-left: 4px solid {{ $primary_color }};
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .warning-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .alert-box {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        /* Pied de page */
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid {{ $primary_color }};
            text-align: center;
            font-size: 10px;
            color: #64748b;
        }
        
        .footer-contact {
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }
        
        .footer-contact div {
            flex: 1;
            text-align: center;
        }
        
        .footer-contact strong {
            color: {{ $secondary_color }};
        }
        
        .footer-note {
            font-style: italic;
            margin-top: 15px;
            color: #9ca3af;
        }
        
        /* Types de documents spécifiques */
        .document-type-ordonnance .document-title h2 {
            background: linear-gradient(135deg, #dc262615, #991b1b10);
            border-color: #dc2626;
            color: #991b1b;
        }
        
        .document-type-consultation .document-title h2 {
            background: linear-gradient(135deg, #3b82f615, #1d4ed810);
            border-color: #3b82f6;
            color: #1d4ed8;
        }
        
        .document-type-analyse .document-title h2 {
            background: linear-gradient(135deg, #8b5cf615, #7c3aed10);
            border-color: #8b5cf6;
            color: #7c3aed;
        }
        
        .document-type-ticket .document-title h2 {
            background: linear-gradient(135deg, #f59e0b15, #d9770610);
            border-color: #f59e0b;
            color: #d97706;
        }
        
        /* Responsive pour impression */
        @media print {
            .header-section {
                margin-bottom: 15px;
            }
            
            .content-section {
                margin: 15px 0;
            }
            
            .data-table {
                font-size: 9px;
            }
            
            body {
                font-size: 10pt;
            }
        }
        
        /* Utilitaires */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 10px; }
        .text-lg { font-size: 14px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-4 { margin-top: 16px; }
        .p-3 { padding: 12px; }
        
        /* Couleurs */
        .text-success { color: {{ $primary_color }}; }
        .text-danger { color: #ef4444; }
        .text-warning { color: #f59e0b; }
        .text-info { color: #3b82f6; }
        .text-muted { color: #64748b; }
    </style>
    
    @stack('pdf-styles')
</head>
<body class="document-type-{{ $document_type }}">
    <!-- En-tête du document -->
    <div class="header-section">
        <div class="logo-section">
            <div class="logo">
                SH
            </div>
            <div class="brand-info">
                <h1>{{ $platform_name }}</h1>
                <div class="tagline">{{ $address }}</div>
            </div>
        </div>
        
        <div class="document-info">
            <div class="doc-number">{{ $document_number }}</div>
            <div class="doc-date">Généré le {{ $generated_at }}</div>
            <div>{{ $website }}</div>
        </div>
    </div>
    
    <!-- Titre du document -->
    <div class="document-title">
        <h2>{{ $title }}</h2>
    </div>
    
    <!-- Contenu principal -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Pied de page -->
    <div class="footer-section">
        <div class="footer-contact">
            <div>
                <strong>Téléphone:</strong><br>
                {{ $phone }}
            </div>
            <div>
                <strong>Email:</strong><br>
                {{ $email }}
            </div>
            <div>
                <strong>Site web:</strong><br>
                {{ $website }}
            </div>
        </div>
        
        <div class="footer-note">
            Ce document a été généré automatiquement par la plateforme {{ $platform_name }}.
            <br>Pour toute question, contactez notre support technique.
        </div>
    </div>
    
    @stack('pdf-scripts')
</body>
</html>
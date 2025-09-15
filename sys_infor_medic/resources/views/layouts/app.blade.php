<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SMART-HEALTH</title>

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        /* Police principale */
        body {
            background-color: #f0f9f4;
            font-family: 'Roboto', sans-serif;
            color: #2c3e50;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 40px;
        }

        /* Navbar */
        nav.navbar {
            background-color: #e6f4ea !important;
            box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
        }

        nav.navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #27ae60 !important;
            text-transform: uppercase;
        }

        nav.navbar .navbar-brand img {
            filter: drop-shadow(0 0 1px rgba(39, 174, 96, 0.7));
        }

        /* Container général */
        .container {
            max-width: 1100px;
        }

        /* Cartes */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgb(39 174 96 / 0.1);
            transition: transform 0.15s ease-in-out;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 30px rgb(39 174 96 / 0.2);
        }

        .card-header {
            background-color: #c2e9c9 !important;
            font-weight: 700;
            font-size: 1.2rem;
            color: #145a32;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        /* Tabs */
        .nav-tabs .nav-link {
            color: #27ae60;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s;
        }

        .nav-tabs .nav-link.active {
            border-color: #27ae60;
            color: #145a32;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            color: #52b788;
        }

        /* Boutons */
        .btn-success {
            background-color: #27ae60;
            border: none;
        }

        .btn-success:hover {
            background-color: #1e8449;
        }

        .btn-primary {
            background-color: #2d7a2b;
            border: none;
        }

        .btn-primary:hover {
            background-color: #23641f;
        }

        .btn-danger {
            background-color: #c0392b;
            border: none;
        }

        .btn-danger:hover {
            background-color: #922b21;
        }

        /* Table */
        table.table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.05);
        }

        table.table thead {
            background-color: #a3d9a5;
            color: #145a32;
        }

        /* Boutons actions */
        button.btn-sm {
            min-width: 70px;
        }
    </style>
    <!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js'></script>

</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container">
            <a class="navbar-brand text-success d-flex align-items-center" href="#">
                <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo" width="40" height="40" class="me-2">
                SMART-HEALTH
            </a>
        </div>
    </nav>

    {{-- Contenu de la page --}}
    <div class="container mt-4">
        @yield('content')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Zone pour scripts spécifiques aux vues --}}
    @yield('scripts')

</body>
</html>

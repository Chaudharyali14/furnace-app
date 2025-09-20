<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Furnace Management')</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
        }

        /* Sidebar fix */
        #sidebarMenu {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            z-index: 1050;
            border-right: 1px solid rgba(0,0,0,0.1);
        }

        /* Main content sidebar ke sath shift ho */
        .main-content {
            padding-top: 70px;   /* navbar ke liye */
            margin-left: 250px;  /* sidebar ke liye */
        }

        /* Mobile view */
        @media (max-width: 991px) {
            #sidebarMenu {
                position: fixed;
                width: 250px;
                z-index: 1050;
            }
            .main-content {
                margin-left: 0;
            }
        }

        @media (min-width: 992px) {
            .navbar-toggler {
                display: none;
            }
            #sidebarMenu {
                transform: none !important;
                visibility: visible !important;
            }
            .offcanvas-backdrop {
                display: none !important;
            }
        }

        .dark-mode {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }

        .dark-mode .card {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }

        .dark-mode .navbar {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
        }

        .dark-mode .navbar-brand, .dark-mode .nav-link, .dark-mode .navbar-toggler-icon {
            color: #e0e0e0 !important;
        }

        .dark-mode #sidebarMenu {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
        }

        .dark-mode .list-group-item {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }

        .dark-mode .list-group-item:hover {
            background-color: #333 !important;
        }

        .dark-mode .form-control {
            background-color: #2c2c2c !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }

        .dark-mode .form-select {
            background-color: #2c2c2c !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ session('theme', 'light') === 'dark' ? 'dark-mode' : '' }}">

    <!-- Navbar -->
    <nav class="navbar navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Furnace MS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" 
                    data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Libraries for Export Buttons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf-autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }

        main {
        flex: 1 0 auto;
        display: flex;
        align-items: center; 
        padding: 60px 0; 
        }

        .container {
            max-width: 1140px;
        }

        footer {
            flex-shrink: 0;
            background-color: #fff;
            border-top: 1px solid #dee2e6;
            padding: 10px 0 !important;
        }

        .table th, .table td {
            padding: 12px 15px !important; 
            vertical-align: middle;
        }
        
        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            border-radius: 12px !important;
            background-color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden; /* Memastikan isi di dalam mengikuti lekukan card */
        }

        .card-header {
            border-top-left-radius: 11px !important; /* Sedikit lebih kecil dari card agar pas */
            border-top-right-radius: 11px !important;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="/">SISTEM INVENTORY</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link {{ Request::is('maintenance-stok') ? 'active' : '' }}" href="/maintenance-stok">Pemeliharaan</a>
                    <a class="nav-link {{ Request::is('report-saldo') ? 'active' : '' }}" href="/report-saldo">Laporan Saldo</a>
                    <a class="nav-link {{ Request::is('report-history') ? 'active' : '' }}" href="/report-history">Riwayat Transaksi</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4"> 
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container text-center">
            <span class="text-muted small">© 2026 Grace - Magang PRIME Tugas Akhir 1</span>
        </div>
    </footer>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
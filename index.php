<?php
session_start();
require_once 'config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// İstatistikleri çek
$stats = [
    'personel_sayisi' => $db->query("SELECT COUNT(*) FROM personel")->fetchColumn(),
    'firma_sayisi' => $db->query("SELECT COUNT(*) FROM firmalar")->fetchColumn(),
    'is_sayisi' => $db->query("SELECT COUNT(*) FROM isler")->fetchColumn(),
    'devam_eden_is' => $db->query("SELECT COUNT(*) FROM isler WHERE durum = 'devam_ediyor'")->fetchColumn()
];

// Aktif menü öğesini belirle
$active_menu = "dashboard";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Personel Takip Sistemi</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar Stilleri */
        .sidebar {
            width: 220px;
            background: #343a40;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: width 0.3s ease;
        }
        
        .sidebar-title {
            padding: 20px 15px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link {
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            transition: 0.3s;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            color: #fff;
            background: #007bff;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: #fff;
            font-size: 20px;
            padding: 10px;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .sidebar.collapsed {
            width: 60px;
        }
        
        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        /* Ana İçerik Alanı */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            background: #f8f9fa;
        }
        
        .sidebar.collapsed ~ .main-content {
            margin-left: 60px;
        }
        
        /* İçerik Başlık Satırı */
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            margin: -20px -20px 20px -20px;
            border-bottom: 1px solid #dee2e6;
            background: #fff;
        }
        
        .content-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        /* Stat Kartları */
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
            border: none;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        
        .stat-card .card-body {
            padding: 20px;
            text-align: center;
        }
        
        .stat-card i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .stat-card h5 {
            font-size: 1rem;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .stat-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        /* Responsive Ayarlamalar */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .sidebar:not(.collapsed) {
                width: 220px;
                box-shadow: 0 0 15px rgba(0,0,0,0.2);
            }
            .main-content {
                margin-left: 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <button class="sidebar-toggle" id="sidebarToggle" title="Menüyü Aç/Kapat">
            <i class="fas fa-bars"></i>
        </button>
        <div class="sidebar-title">Personel Takip</div>
        <div class="nav flex-column">
            <a class="nav-link active" href="index.php">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link" href="personel_ekle.php">
                <i class="fas fa-user-plus"></i> <span>Personel Ekle</span>
            </a>
            <a class="nav-link" href="personel_listesi.php">
                <i class="fas fa-users"></i> <span>Personel Listesi</span>
            </a>
            <a class="nav-link" href="firma_ekle.php">
                <i class="fas fa-building"></i> <span>Firma Ekle</span>
            </a>
            <a class="nav-link" href="firma_listesi.php">
                <i class="fas fa-building"></i> <span>Firma Listesi</span>
            </a>
            <a class="nav-link" href="is_ver.php">
                <i class="fas fa-tasks"></i> <span>İş Ver</span>
            </a>
            <a class="nav-link" href="is_listesi.php">
                <i class="fas fa-list"></i> <span>İş Listesi</span>
            </a>
            <a class="nav-link" href="raporlar.php">
                <i class="fas fa-chart-bar"></i> <span>Raporlar</span>
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> <span>Çıkış</span>
            </a>
        </div>
    </nav>

    <!-- Ana İçerik -->
    <main class="main-content">
        <!-- İçerik Başlığı -->
        <div class="content-header">
            <h2>Dashboard</h2>
            <div>
                <span class="me-3">Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['ad_soyad']); ?></span>
            </div>
        </div>

<!-- İstatistik Kartları -->
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
        <a href="personel_listesi.php" class="text-decoration-none">
            <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                    <i class="fas fa-users"></i>
                    <h5>Toplam Personel</h5>
                            <h2><?php echo $stats['personel_sayisi']; ?></h2>
                </div>
            </div>
        </a>
    </div>
            <div class="col-md-3 col-sm-6">
        <a href="firma_listesi.php" class="text-decoration-none">
            <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                    <i class="fas fa-building"></i>
                    <h5>Toplam Firma</h5>
                            <h2><?php echo $stats['firma_sayisi']; ?></h2>
                </div>
            </div>
        </a>
    </div>
            <div class="col-md-3 col-sm-6">
        <a href="is_listesi.php" class="text-decoration-none">
            <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                    <i class="fas fa-tasks"></i>
                    <h5>Toplam İş</h5>
                            <h2><?php echo $stats['is_sayisi']; ?></h2>
                </div>
            </div>
        </a>
    </div>
            <div class="col-md-3 col-sm-6">
        <a href="is_listesi.php?durum=devam_ediyor" class="text-decoration-none">
            <div class="card stat-card bg-warning text-white">
                        <div class="card-body">
                    <i class="fas fa-spinner"></i>
                    <h5>Devam Eden İş</h5>
                            <h2><?php echo $stats['devam_eden_is']; ?></h2>
                </div>
            </div>
        </a>
    </div>
</div>
    </main>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar aç/kapa
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            
            // Local storage'dan sidebar durumunu kontrol et
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
            
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });
        });
    </script>
</body>
</html> 
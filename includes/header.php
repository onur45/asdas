<?php
// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Sayfa başlığı ve aktif sayfa değişkenlerini ayarla
$sayfa_basligi = isset($sayfa_basligi) ? $sayfa_basligi : "Dashboard";
$aktif_sayfa = isset($aktif_sayfa) ? $aktif_sayfa : "dashboard";

// Menü yapısını tanımla
$menu_items = [
    'dashboard' => [
        'title' => 'Dashboard',
        'icon' => 'fas fa-home',
        'url' => 'index.php'
    ],
    'personel_ekle' => [
        'title' => 'Personel Ekle',
        'icon' => 'fas fa-user-plus',
        'url' => 'personel_ekle.php'
    ],
    'personel_listesi' => [
        'title' => 'Personel Listesi',
        'icon' => 'fas fa-users',
        'url' => 'personel_listesi.php'
    ],
    'firma_ekle' => [
        'title' => 'Firma Ekle',
        'icon' => 'fas fa-building',
        'url' => 'firma_ekle.php'
    ],
    'firma_listesi' => [
        'title' => 'Firma Listesi',
        'icon' => 'fas fa-building',
        'url' => 'firma_listesi.php'
    ],
    'is_ver' => [
        'title' => 'İş Ver',
        'icon' => 'fas fa-tasks',
        'url' => 'is_ver.php'
    ],
    'is_listesi' => [
        'title' => 'İş Listesi',
        'icon' => 'fas fa-list',
        'url' => 'is_listesi.php'
    ],
    'raporlar' => [
        'title' => 'Raporlar',
        'icon' => 'fas fa-chart-bar',
        'url' => 'raporlar.php'
    ]
];

// Aktif menü öğesini belirle
$active_menu = $aktif_sayfa;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sayfa_basligi; ?> - Personel Takip Sistemi</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/tr.js"></script>
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
        
        /* İçerik Kapsayıcı */
        .content-body {
            padding: 20px;
            margin: 0;
        }

        /* Bootstrap row ve col elementlerini sıkı kontrol */
        .content-body .row {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
            margin-top: 0 !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        
        .content-body .row > * {
            padding-top: 0 !important;
        }

        /* Debug için yardımcı sınıf - boşlukları görselleştirmek için */
        .debug-border {
            border: 1px solid red !important;
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
            .sidebar.collapsed ~ .main-content {
                margin-left: 60px;
            }
            .sidebar:not(.collapsed) ~ .main-content {
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
            <a class="nav-link <?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>" href="index.php">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link <?php echo $active_menu === 'personel_ekle' ? 'active' : ''; ?>" href="personel_ekle.php">
                <i class="fas fa-user-plus"></i> <span>Personel Ekle</span>
            </a>
            <a class="nav-link <?php echo $active_menu === 'personel_listesi' ? 'active' : ''; ?>" href="personel_listesi.php">
                <i class="fas fa-users"></i> <span>Personel Listesi</span>
            </a>
            <a class="nav-link <?php echo $active_menu === 'firma_ekle' ? 'active' : ''; ?>" href="firma_ekle.php">
                <i class="fas fa-building"></i> <span>Firma Ekle</span>
            </a>
            <a class="nav-link <?php echo $active_menu === 'firma_listesi' ? 'active' : ''; ?>" href="firma_listesi.php">
                <i class="fas fa-building"></i> <span>Firma Listesi</span>
            </a>
            <a class="nav-link <?php echo $active_menu === 'is_ver' ? 'active' : ''; ?>" href="is_ver.php">
                <i class="fas fa-tasks"></i> <span>İş Ver</span>
            </a>
            <a class="nav-link <?php echo $active_menu === 'is_listesi' ? 'active' : ''; ?>" href="is_listesi.php">
                <i class="fas fa-list"></i> <span>İş Listesi</span>
            </a>
            <a class="nav-link <?php echo $active_menu === 'raporlar' ? 'active' : ''; ?>" href="raporlar.php">
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
            <h2><?php echo $sayfa_basligi; ?></h2>
            <div>
                <span class="me-3">Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['ad_soyad']); ?></span>
            </div>
        </div>

        <!-- Bildirimler -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success mb-3">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger mb-3">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Ana içerik buraya gelecek -->
    </main>

    <!-- Ortak Modal -->
    <div class="modal fade" id="duzenleModal" tabindex="-1" aria-labelledby="duzenleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duzenleModalLabel">Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body" id="duzenleModalBody">
                    <!-- AJAX ile içerik yüklenecek -->
                </div>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(url) {
        const modal = new bootstrap.Modal(document.getElementById('duzenleModal'));
        document.getElementById('duzenleModalBody').innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('duzenleModalBody').innerHTML = html;
            });
        modal.show();
    }
    
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
            // Durumu local storage'a kaydet
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    });
    </script>
</body>
</html> 
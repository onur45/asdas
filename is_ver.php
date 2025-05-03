<?php
require_once 'config/db.php';
session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sayfa_basligi = "İş Ver";
$aktif_sayfa = "is_ver";

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $personel_id = $_POST['personel_id'];
    $firma_id = $_POST['firma_id'];
    $is_aciklama = $_POST['is_aciklama'];
    $baslangic_tarihi = $_POST['baslangic_tarihi'];
    $bitis_tarihi = $_POST['bitis_tarihi'];
    $tutar = $_POST['tutar'];
    $durum = 'devam_ediyor';
    
    $stmt = $db->prepare("INSERT INTO isler (personel_id, firma_id, is_aciklama, baslangic_tarihi, bitis_tarihi, tutar, durum) 
                         VALUES (:personel_id, :firma_id, :is_aciklama, :baslangic_tarihi, :bitis_tarihi, :tutar, :durum)");
    
    $stmt->bindParam(':personel_id', $personel_id);
    $stmt->bindParam(':firma_id', $firma_id);
    $stmt->bindParam(':is_aciklama', $is_aciklama);
    $stmt->bindParam(':baslangic_tarihi', $baslangic_tarihi);
    $stmt->bindParam(':bitis_tarihi', $bitis_tarihi);
    $stmt->bindParam(':tutar', $tutar);
    $stmt->bindParam(':durum', $durum);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "İş başarıyla eklendi.";
        header("Location: is_listesi.php");
        exit();
    } else {
        $_SESSION['error'] = "İş eklenirken bir hata oluştu.";
    }
}

// Personelleri çek
$stmt = $db->query("SELECT p.*, i.ilce_adi 
                    FROM personel p 
                    LEFT JOIN ilceler i ON p.ilce_id = i.id 
                    ORDER BY p.ad, p.soyad");
$personeller = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Firmaları çek
$stmt = $db->query("SELECT * FROM firmalar ORDER BY firma_adi");
$firmalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Personel</label>
                        <select name="personel_id" required id="personel-select">
                            <option value="">Personel Seçin</option>
                            <?php foreach ($personeller as $personel): ?>
                                <option value="<?php echo $personel['id']; ?>">
                                    <?php echo htmlspecialchars($personel['ad'] . ' ' . $personel['soyad'] . ' (' . $personel['ilce_adi'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Firma</label>
                        <select name="firma_id" required id="firma-select">
                            <option value="">Firma Seçin</option>
                            <?php foreach ($firmalar as $firma): ?>
                                <option value="<?php echo $firma['id']; ?>">
                                    <?php echo htmlspecialchars($firma['firma_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">İş Açıklaması</label>
                    <textarea name="is_aciklama" class="form-control" rows="3" required></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Başlangıç Tarihi</label>
                        <input type="date" name="baslangic_tarihi" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bitiş Tarihi</label>
                        <input type="date" name="bitis_tarihi" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tutar</label>
                    <input type="number" name="tutar" class="form-control" step="0.01" required>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="is_listesi.php" class="btn btn-secondary">Geri Dön</a>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Personel select2
    $('#personel-select').select2({
        theme: 'bootstrap4',
        placeholder: "",
        allowClear: true,
        language: "tr",
        width: '100%',
        dropdownParent: $('#personel-select').parent(),
        minimumResultsForSearch: 0,
        templateResult: formatResult,
        templateSelection: formatSelection
    }).on('select2:open', function() {
        setTimeout(function() {
            $('.select2-search__field').focus();
        }, 0);
    });

    // Firma select2
    $('#firma-select').select2({
        theme: 'bootstrap4',
        placeholder: "",
        allowClear: true,
        language: "tr",
        width: '100%',
        dropdownParent: $('#firma-select').parent(),
        minimumResultsForSearch: 0,
        templateResult: formatResult,
        templateSelection: formatSelection
    }).on('select2:open', function() {
        setTimeout(function() {
            $('.select2-search__field').focus();
        }, 0);
    });

    // Arama ikonu için format fonksiyonu
    function formatResult(data) {
        if (!data.id) return data.text;
        return $('<span><i class="fas fa-search me-2"></i>' + data.text + '</span>');
    }

    function formatSelection(data) {
        return data.text;
    }
});
</script>

<?php include 'includes/footer.php'; ?> 
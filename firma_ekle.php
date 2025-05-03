<?php
require_once 'config/db.php';
session_start();

$sayfa_basligi = "Firma Ekle";
$aktif_sayfa = "firma_ekle";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firma_adi = $_POST['firma_adi'];
    $notlar = $_POST['notlar'];

    $sql = "INSERT INTO firmalar (firma_adi, notlar) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    
    try {
        $stmt->execute([$firma_adi, $notlar]);
        $_SESSION['success'] = "Firma başarıyla eklendi.";
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Hata oluştu: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

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

        <form method="POST" action="">
            <div class="mb-3">
                <label for="firma_adi" class="form-label">Firma Adı</label>
                <input type="text" class="form-control" id="firma_adi" name="firma_adi" required>
            </div>
            <div class="mb-3">
                <label for="notlar" class="form-label">Notlar</label>
                <textarea class="form-control" id="notlar" name="notlar" rows="3"></textarea>
            </div>
            <div class="d-flex justify-content-end">
                <a href="index.php" class="btn btn-secondary me-2">İptal</a>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
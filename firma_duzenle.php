<?php
session_start();
require_once 'config/db.php';

$sayfa_basligi = "Firma Düzenle";
$aktif_sayfa = "firma_duzenle";

// Firma ID'sini al
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $sql = "DELETE FROM firmalar WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    try {
        $stmt->execute([$id]);
        $_SESSION['success'] = "Firma başarıyla silindi.";
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Hata oluştu: " . $e->getMessage();
    }
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $firma_adi = $_POST['firma_adi'];
    $notlar = $_POST['notlar'];

    $sql = "UPDATE firmalar SET firma_adi = ?, notlar = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    try {
        $stmt->execute([$firma_adi, $notlar, $id]);
        $_SESSION['success'] = "Firma başarıyla güncellendi.";
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Hata oluştu: " . $e->getMessage();
    }
}

// Firma bilgilerini çek
$sql = "SELECT * FROM firmalar WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$firma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$firma) {
    $_SESSION['error'] = "Firma bulunamadı.";
    header("Location: index.php");
    exit();
}

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $ajax_mode = true;
} else {
    $ajax_mode = false;
}

if (!$ajax_mode) include 'includes/header.php';
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
                <input type="text" class="form-control" id="firma_adi" name="firma_adi" value="<?php echo htmlspecialchars($firma['firma_adi']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="notlar" class="form-label">Notlar</label>
                <textarea class="form-control" id="notlar" name="notlar" rows="3"><?php echo htmlspecialchars($firma['notlar']); ?></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-danger" onclick="deleteFirma()">Sil</button>
                <div>
                    <a href="index.php" class="btn btn-secondary me-2">İptal</a>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Silme Onay Modalı -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Firmayı Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu firmayı silmek istediğinizden emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="delete">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteFirma() {
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php if (!$ajax_mode) include 'includes/footer.php'; ?> 
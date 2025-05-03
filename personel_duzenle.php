<?php
session_start();
require_once 'config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sayfa_basligi = "Personel Düzenle";
$aktif_sayfa = "personel_duzenle";

// Personel ID'si kontrolü
if (!isset($_GET['id'])) {
    header("Location: personel_listesi.php");
    exit();
}

$id = $_GET['id'];

// Personel bilgilerini çek
$stmt = $db->prepare("SELECT p.*, i.ilce_adi 
                      FROM personel p 
                      LEFT JOIN ilceler i ON p.ilce_id = i.id 
                      WHERE p.id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$personel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$personel) {
    header("Location: personel_listesi.php");
    exit();
}

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        // Personeli sil
        $stmt = $db->prepare("DELETE FROM personel WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Personel başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Personel silinirken bir hata oluştu.";
        }
        
        header("Location: personel_listesi.php");
        exit();
    } else {
        // Personeli güncelle
        $ad = $_POST['ad'];
        $soyad = $_POST['soyad'];
        $telefon = $_POST['telefon'];
        $ilce_id = $_POST['ilce_id'];
        $notlar = $_POST['notlar'];
        
        $stmt = $db->prepare("UPDATE personel SET 
                             ad = :ad,
                             soyad = :soyad,
                             telefon = :telefon,
                             ilce_id = :ilce_id,
                             notlar = :notlar
                             WHERE id = :id");
        
        $stmt->bindParam(':ad', $ad);
        $stmt->bindParam(':soyad', $soyad);
        $stmt->bindParam(':telefon', $telefon);
        $stmt->bindParam(':ilce_id', $ilce_id);
        $stmt->bindParam(':notlar', $notlar);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Personel başarıyla güncellendi.";
        } else {
            $_SESSION['error'] = "Personel güncellenirken bir hata oluştu.";
        }
        
        header("Location: personel_listesi.php");
        exit();
    }
}

// İlçeleri çek
$stmt = $db->query("SELECT * FROM ilceler ORDER BY ilce_adi");
$ilceler = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Ad</label>
                    <input type="text" name="ad" class="form-control" value="<?php echo htmlspecialchars($personel['ad']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Soyad</label>
                    <input type="text" name="soyad" class="form-control" value="<?php echo htmlspecialchars($personel['soyad']); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Telefon</label>
                    <input type="tel" name="telefon" class="form-control" value="<?php echo htmlspecialchars($personel['telefon']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">İlçe</label>
                    <select name="ilce_id" class="form-select" required>
                        <option value="">İlçe Seçin</option>
                        <?php foreach ($ilceler as $ilce): ?>
                            <option value="<?php echo $ilce['id']; ?>" <?php echo ($ilce['id'] == $personel['ilce_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ilce['ilce_adi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Notlar</label>
                <textarea name="notlar" class="form-control" rows="3"><?php echo htmlspecialchars($personel['notlar']); ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="personel_listesi.php" class="btn btn-secondary">Geri Dön</a>
                <div>
                    <button type="button" class="btn btn-danger" onclick="deletePersonel()">Sil</button>
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
                <h5 class="modal-title">Personeli Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu personeli silmek istediğinizden emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deletePersonel() {
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php if (!$ajax_mode) include 'includes/footer.php'; ?> 
<?php
require_once 'config/db.php';
session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sayfa_basligi = "İş Düzenle";
$aktif_sayfa = "is_duzenle";

// İş ID'si kontrolü
if (!isset($_GET['id'])) {
    header("Location: is_listesi.php");
    exit();
}

$id = $_GET['id'];

// İş bilgilerini çek
$stmt = $db->prepare("SELECT i.*, p.ad, p.soyad, f.firma_adi 
                      FROM isler i 
                      LEFT JOIN personel p ON i.personel_id = p.id 
                      LEFT JOIN firmalar f ON i.firma_id = f.id 
                      WHERE i.id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$is = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$is) {
    header("Location: is_listesi.php");
    exit();
}

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete') {
            // İşi sil
            $stmt = $db->prepare("DELETE FROM isler WHERE id = :id");
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "İş başarıyla silindi.";
            } else {
                $_SESSION['error'] = "İş silinirken bir hata oluştu.";
            }
            
            header("Location: is_listesi.php");
            exit();
        } elseif ($_POST['action'] == 'tamamla') {
            // İşi tamamla
            $stmt = $db->prepare("UPDATE isler SET 
                                 durum = 'tamamlandi',
                                 bitis_tarihi = CURRENT_DATE()
                                 WHERE id = :id");
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "İş başarıyla tamamlandı.";
            } else {
                $_SESSION['error'] = "İş tamamlanırken bir hata oluştu.";
            }
            
            header("Location: is_listesi.php");
            exit();
        }
    } else {
        // İşi güncelle
        $durum = $_POST['durum'];
        $bitis_tarihi = $_POST['bitis_tarihi'] ?: null;
        
        $stmt = $db->prepare("UPDATE isler SET 
                             durum = :durum,
                             bitis_tarihi = :bitis_tarihi
                             WHERE id = :id");
        
        $stmt->bindParam(':durum', $durum);
        $stmt->bindParam(':bitis_tarihi', $bitis_tarihi);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "İş başarıyla güncellendi.";
        } else {
            $_SESSION['error'] = "İş güncellenirken bir hata oluştu.";
        }
        
        header("Location: is_listesi.php");
        exit();
    }
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
        <form method="POST" id="isForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Personel</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($is['ad'] . ' ' . $is['soyad']); ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Firma</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($is['firma_adi']); ?>" readonly>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Durum</label>
                    <select name="durum" class="form-select" required>
                        <option value="devam_ediyor" <?php echo $is['durum'] == 'devam_ediyor' ? 'selected' : ''; ?>>Devam Ediyor</option>
                        <option value="tamamlandi" <?php echo $is['durum'] == 'tamamlandi' ? 'selected' : ''; ?>>Tamamlandı</option>
                        <option value="iptal_edildi" <?php echo $is['durum'] == 'iptal_edildi' ? 'selected' : ''; ?>>İptal Edildi</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Başlangıç Tarihi</label>
                    <input type="date" name="baslangic_tarihi" class="form-control" value="<?php echo $is['baslangic_tarihi']; ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bitiş Tarihi</label>
                    <input type="date" name="bitis_tarihi" class="form-control" value="<?php echo $is['bitis_tarihi']; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tutar</label>
                    <input type="text" class="form-control" value="<?php echo number_format($is['tutar'], 2, ',', '.'); ?> TL" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Firma Karı</label>
                    <input type="text" class="form-control" value="<?php echo number_format($is['firma_kari'], 2, ',', '.'); ?> TL" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Miktar</label>
                    <input type="text" class="form-control" value="<?php echo $is['miktar'] . ' ' . $is['birim']; ?>" readonly>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="is_listesi.php" class="btn btn-secondary">Geri Dön</a>
                <div>
                    <button type="button" class="btn btn-danger" onclick="deleteIs()">Sil</button>
                    <button type="button" class="btn btn-success" onclick="tamamlaIs()">Tamamla</button>
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
                <h5 class="modal-title">İşi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu işi silmek istediğinizden emin misiniz?</p>
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

<!-- Tamamlama Onay Modalı -->
<div class="modal fade" id="tamamlaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İşi Tamamla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu işi tamamlamak istediğinizden emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="action" value="tamamla">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">Tamamla</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteIs() {
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function tamamlaIs() {
    var modal = new bootstrap.Modal(document.getElementById('tamamlaModal'));
    modal.show();
}
</script>

<?php if (!$ajax_mode) include 'includes/footer.php'; ?> 
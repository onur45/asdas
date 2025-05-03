<?php
require_once 'config/db.php';
session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sayfa_basligi = "Personel Ekle";
$aktif_sayfa = "personel_ekle";

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $telefon = $_POST['telefon'];
    $ilce_id = $_POST['ilce_id'];
    $notlar = $_POST['notlar'];
    
    $stmt = $db->prepare("INSERT INTO personel (ad, soyad, telefon, ilce_id, notlar) VALUES (:ad, :soyad, :telefon, :ilce_id, :notlar)");
    
    $stmt->bindParam(':ad', $ad);
    $stmt->bindParam(':soyad', $soyad);
    $stmt->bindParam(':telefon', $telefon);
    $stmt->bindParam(':ilce_id', $ilce_id);
    $stmt->bindParam(':notlar', $notlar);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Personel başarıyla eklendi.";
        header("Location: personel_listesi.php");
        exit();
    } else {
        $_SESSION['error'] = "Personel eklenirken bir hata oluştu.";
    }
}

// İlçeleri çek
$stmt = $db->query("SELECT * FROM ilceler ORDER BY ilce_adi");
$ilceler = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Ad</label>
                    <input type="text" name="ad" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Soyad</label>
                    <input type="text" name="soyad" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Telefon</label>
                    <input type="tel" name="telefon" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">İlçe</label>
                    <select name="ilce_id" class="form-select" required>
                        <option value="">İlçe Seçin</option>
                        <?php foreach ($ilceler as $ilce): ?>
                            <option value="<?php echo $ilce['id']; ?>">
                                <?php echo htmlspecialchars($ilce['ilce_adi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Notlar</label>
                <textarea name="notlar" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="personel_listesi.php" class="btn btn-secondary">Geri Dön</a>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
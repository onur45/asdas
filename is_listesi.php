<?php
require_once 'config/db.php';
session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sayfa_basligi = "İş Listesi";
$aktif_sayfa = "is_listesi";

// Durum filtresi
$durum = isset($_GET['durum']) ? $_GET['durum'] : 'tumu';

// İşleri çek
$sql = "SELECT i.*, p.ad, p.soyad, p.telefon, f.firma_adi 
        FROM isler i 
        LEFT JOIN personel p ON i.personel_id = p.id 
        LEFT JOIN firmalar f ON i.firma_id = f.id";

// Durum filtresine göre WHERE koşulu ekle
if ($durum != 'tumu') {
    $sql .= " WHERE i.durum = :durum";
}

$sql .= " ORDER BY i.baslangic_tarihi DESC";

$stmt = $db->prepare($sql);
if ($durum != 'tumu') {
    $stmt->bindParam(':durum', $durum);
}
$stmt->execute();
$isler = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="card">
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Durum Filtreleri -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="?durum=tumu" class="btn btn-outline-primary <?php echo $durum == 'tumu' ? 'active' : ''; ?>">
                    Tümü
                </a>
                <a href="?durum=devam_ediyor" class="btn btn-outline-warning <?php echo $durum == 'devam_ediyor' ? 'active' : ''; ?>">
                    Devam Edenler
                </a>
                <a href="?durum=tamamlandi" class="btn btn-outline-success <?php echo $durum == 'tamamlandi' ? 'active' : ''; ?>">
                    Tamamlananlar
                </a>
                <a href="?durum=iptal_edildi" class="btn btn-outline-danger <?php echo $durum == 'iptal_edildi' ? 'active' : ''; ?>">
                    İptal Edilenler
                </a>
            </div>
        </div>

        <!-- Arama Kutusu -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="İş veya personel ara...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" id="isTable">
                <thead>
                    <tr>
                        <th>Personel</th>
                        <th>Firma</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($isler as $is): ?>
                        <tr class="is-row" data-id="<?php echo $is['id']; ?>">
                            <td><?php echo htmlspecialchars($is['ad'] . ' ' . $is['soyad']); ?></td>
                            <td><?php echo htmlspecialchars($is['firma_adi']); ?></td>
                            <td><?php echo number_format($is['tutar'], 2, ',', '.'); ?> TL</td>
                            <td>
                                <?php
                                switch ($is['durum']) {
                                    case 'devam_ediyor':
                                        echo '<span class="badge bg-warning">Devam Ediyor</span>';
                                        break;
                                    case 'tamamlandi':
                                        echo '<span class="badge bg-success">Tamamlandı</span>';
                                        break;
                                    case 'iptal_edildi':
                                        echo '<span class="badge bg-danger">İptal Edildi</span>';
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $is['telefon']); ?>" 
                                       class="btn btn-sm btn-success" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="tel:<?php echo $is['telefon']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
                <form method="POST" action="is_duzenle.php">
                    <input type="hidden" name="id" id="delete_id">
                    <input type="hidden" name="action" value="delete">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteIs(id) {
    document.getElementById('delete_id').value = id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Satıra tıklayınca düzenle
const rows = document.querySelectorAll('.is-row');
rows.forEach(function(row) {
    row.addEventListener('click', function(e) {
        // Eğer tıklanan buton değilse
        if (!e.target.closest('.btn')) {
            openEditModal('is_duzenle.php?id=' + row.getAttribute('data-id') + '&ajax=1');
        }
    });
});

// Anında arama fonksiyonu

document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = this.value.toLowerCase();
    var table = document.getElementById('isTable');
    var rows = table.getElementsByTagName('tr');

    for (var i = 1; i < rows.length; i++) {
        var row = rows[i];
        var cells = row.getElementsByTagName('td');
        var found = false;

        for (var j = 0; j < cells.length - 1; j++) { // Son sütunu (işlemler) hariç tut
            var cell = cells[j];
            if (cell) {
                var text = cell.textContent || cell.innerText;
                if (text.toLowerCase().indexOf(input) > -1) {
                    found = true;
                    break;
                }
            }
        }

        if (found) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?> 
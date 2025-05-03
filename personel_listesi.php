<?php
require_once 'config/db.php';
session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sayfa_basligi = "Personel Listesi";
$aktif_sayfa = "personel_listesi";

// Personelleri çek
$stmt = $db->query("SELECT p.*, i.ilce_adi 
                    FROM personel p 
                    LEFT JOIN ilceler i ON p.ilce_id = i.id 
                    ORDER BY p.ad, p.soyad");
$personeller = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        <!-- Arama Kutusu -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Personel ara...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" id="personelTable">
                <thead>
                    <tr>
                        <th>Ad Soyad</th>
                        <th>İlçe</th>
                        <th>Notlar</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personeller as $personel): ?>
                        <tr class="personel-row" data-id="<?php echo $personel['id']; ?>">
                            <td><?php echo htmlspecialchars($personel['ad'] . ' ' . $personel['soyad']); ?></td>
                            <td><?php echo htmlspecialchars($personel['ilce_adi']); ?></td>
                            <td><?php echo htmlspecialchars($personel['notlar']); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $personel['telefon']); ?>" 
                                       class="btn btn-sm btn-success" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="tel:<?php echo $personel['telefon']; ?>" class="btn btn-sm btn-info">
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
                <h5 class="modal-title">Personeli Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu personeli silmek istediğinizden emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="personel_duzenle.php">
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
function deletePersonel(id) {
    document.getElementById('delete_id').value = id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Anında arama fonksiyonu
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = this.value.toLowerCase();
    var table = document.getElementById('personelTable');
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

// Satıra tıklayınca düzenle
const personelRows = document.querySelectorAll('.personel-row');
personelRows.forEach(function(row) {
    row.addEventListener('click', function(e) {
        if (!e.target.closest('.btn')) {
            openEditModal('personel_duzenle.php?id=' + row.getAttribute('data-id') + '&ajax=1');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 
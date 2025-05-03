<?php
session_start();
require_once 'config/db.php';

$sayfa_basligi = "Firma Listesi";
$aktif_sayfa = "firma_listesi";

// Firma listesini çek
$sql = "SELECT * FROM firmalar ORDER BY firma_adi";
$stmt = $db->query($sql);
$firmalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Firma Adı</th>
                        <th>Notlar</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($firmalar as $firma): ?>
                    <tr class="firma-row" data-id="<?php echo $firma['id']; ?>">
                        <td><?php echo htmlspecialchars($firma['firma_adi']); ?></td>
                        <td><?php echo htmlspecialchars($firma['notlar']); ?></td>
                        <td></td>
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
                <h5 class="modal-title">Firmayı Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu firmayı silmek istediğinizden emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="firma_duzenle.php">
                    <input type="hidden" name="id" id="deleteFirmaId">
                    <input type="hidden" name="action" value="delete">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteFirma(id) {
    document.getElementById('deleteFirmaId').value = id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Satıra tıklayınca düzenle
const firmaRows = document.querySelectorAll('.firma-row');
firmaRows.forEach(function(row) {
    row.addEventListener('click', function(e) {
        openEditModal('firma_duzenle.php?id=' + row.getAttribute('data-id') + '&ajax=1');
    });
});
</script>

<?php include 'includes/footer.php'; ?> 
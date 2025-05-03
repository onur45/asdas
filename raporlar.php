<?php
require_once 'config/db.php';
session_start();

$sayfa_basligi = "Raporlar";
$aktif_sayfa = "raporlar";

// Tarih aralığını al
$baslangic_tarihi = isset($_GET['baslangic_tarihi']) ? $_GET['baslangic_tarihi'] : date('Y-m-01');
$bitis_tarihi = isset($_GET['bitis_tarihi']) ? $_GET['bitis_tarihi'] : date('Y-m-d');

// Personel raporu
$sql = "SELECT 
            p.id,
            p.ad,
            p.soyad,
            COUNT(i.id) as is_sayisi,
            SUM(i.tutar) as toplam_tutar,
            SUM(i.firma_kari) as toplam_kar,
            AVG(i.tutar) as ortalama_tutar
        FROM personel p
        LEFT JOIN isler i ON p.id = i.personel_id
        WHERE i.baslangic_tarihi BETWEEN ? AND ?
        GROUP BY p.id, p.ad, p.soyad
        ORDER BY toplam_tutar DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$baslangic_tarihi, $bitis_tarihi]);
$personel_raporu = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Firma raporu
$sql = "SELECT 
            f.id,
            f.firma_adi,
            COUNT(i.id) as is_sayisi,
            SUM(i.tutar) as toplam_tutar,
            SUM(i.firma_kari) as toplam_kar,
            AVG(i.tutar) as ortalama_tutar
        FROM firmalar f
        LEFT JOIN isler i ON f.id = i.firma_id
        WHERE i.baslangic_tarihi BETWEEN ? AND ?
        GROUP BY f.id, f.firma_adi
        ORDER BY toplam_tutar DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$baslangic_tarihi, $bitis_tarihi]);
$firma_raporu = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aylık rapor
$sql = "SELECT 
            DATE_FORMAT(baslangic_tarihi, '%Y-%m') as ay,
            COUNT(id) as is_sayisi,
            SUM(tutar) as toplam_tutar,
            SUM(firma_kari) as toplam_kar,
            AVG(tutar) as ortalama_tutar
        FROM isler
        WHERE baslangic_tarihi BETWEEN ? AND ?
        GROUP BY DATE_FORMAT(baslangic_tarihi, '%Y-%m')
        ORDER BY ay DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$baslangic_tarihi, $bitis_tarihi]);
$aylik_rapor = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="baslangic_tarihi" class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control" id="baslangic_tarihi" name="baslangic_tarihi" 
                       value="<?php echo $baslangic_tarihi; ?>">
            </div>
            <div class="col-md-4">
                <label for="bitis_tarihi" class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control" id="bitis_tarihi" name="bitis_tarihi" 
                       value="<?php echo $bitis_tarihi; ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filtrele</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Personel Raporu</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Personel</th>
                                <th>İş Sayısı</th>
                                <th>Toplam Tutar</th>
                                <th>Toplam Kar</th>
                                <th>Ortalama Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personel_raporu as $rapor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rapor['ad'] . ' ' . $rapor['soyad']); ?></td>
                                    <td><?php echo $rapor['is_sayisi']; ?></td>
                                    <td><?php echo number_format($rapor['toplam_tutar'], 2, ',', '.'); ?> TL</td>
                                    <td><?php echo number_format($rapor['toplam_kar'], 2, ',', '.'); ?> TL</td>
                                    <td><?php echo number_format($rapor['ortalama_tutar'], 2, ',', '.'); ?> TL</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Firma Raporu</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Firma</th>
                                <th>İş Sayısı</th>
                                <th>Toplam Tutar</th>
                                <th>Toplam Kar</th>
                                <th>Ortalama Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($firma_raporu as $rapor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rapor['firma_adi']); ?></td>
                                    <td><?php echo $rapor['is_sayisi']; ?></td>
                                    <td><?php echo number_format($rapor['toplam_tutar'], 2, ',', '.'); ?> TL</td>
                                    <td><?php echo number_format($rapor['toplam_kar'], 2, ',', '.'); ?> TL</td>
                                    <td><?php echo number_format($rapor['ortalama_tutar'], 2, ',', '.'); ?> TL</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aylık Rapor</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Ay</th>
                                <th>İş Sayısı</th>
                                <th>Toplam Tutar</th>
                                <th>Toplam Kar</th>
                                <th>Ortalama Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aylik_rapor as $rapor): ?>
                                <tr>
                                    <td><?php echo date('F Y', strtotime($rapor['ay'] . '-01')); ?></td>
                                    <td><?php echo $rapor['is_sayisi']; ?></td>
                                    <td><?php echo number_format($rapor['toplam_tutar'], 2, ',', '.'); ?> TL</td>
                                    <td><?php echo number_format($rapor['toplam_kar'], 2, ',', '.'); ?> TL</td>
                                    <td><?php echo number_format($rapor['ortalama_tutar'], 2, ',', '.'); ?> TL</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
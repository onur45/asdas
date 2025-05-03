<?php
require_once '../config/db.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM personel WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (ad LIKE ? OR soyad LIKE ? OR telefon LIKE ? OR semt LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
}

$sql .= " ORDER BY ad, soyad";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$personeller = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($personeller as $personel) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($personel['ad'] . ' ' . $personel['soyad']) . '</td>';
    echo '<td>' . htmlspecialchars($personel['telefon']) . '</td>';
    echo '<td>' . htmlspecialchars($personel['semt']) . '</td>';
    echo '<td>' . htmlspecialchars($personel['notlar']) . '</td>';
    echo '<td>';
    echo '<button class="btn btn-sm btn-primary me-1" onclick="callPersonel(\'' . htmlspecialchars($personel['telefon']) . '\')">';
    echo '<i class="fas fa-phone"></i>';
    echo '</button>';
    echo '<button class="btn btn-sm btn-success" onclick="whatsappPersonel(\'' . htmlspecialchars($personel['telefon']) . '\')">';
    echo '<i class="fab fa-whatsapp"></i>';
    echo '</button>';
    echo '</td>';
    echo '</tr>';
}
?> 
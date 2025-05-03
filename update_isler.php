<?php
require_once 'config/db.php';

try {
    // İş açıklaması sütunu ekle
    $db->exec("ALTER TABLE isler ADD COLUMN is_aciklamasi TEXT");
    
    echo "İşler tablosu başarıyla güncellendi.";
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?> 
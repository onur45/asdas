<?php
require_once 'config/db.php';

try {
    // Kullanıcılar tablosunu güncelle
    $sql = "ALTER TABLE kullanicilar 
            ADD COLUMN IF NOT EXISTS ad_soyad VARCHAR(100) NOT NULL AFTER sifre,
            ADD COLUMN IF NOT EXISTS email VARCHAR(100) AFTER ad_soyad,
            ADD COLUMN IF NOT EXISTS son_giris DATETIME AFTER email,
            ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER son_giris";
    
    $db->exec($sql);

    // Varsayılan admin kullanıcısını güncelle
    $sql = "INSERT INTO kullanicilar (kullanici_adi, sifre, ad_soyad, email) 
            SELECT 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sistem Yöneticisi', 'admin@example.com'
            WHERE NOT EXISTS (SELECT 1 FROM kullanicilar WHERE kullanici_adi = 'admin')";
    
    $db->exec($sql);

    echo "Veritabanı başarıyla güncellendi!";
} catch(PDOException $e) {
    echo "Hata oluştu: " . $e->getMessage();
}
?> 
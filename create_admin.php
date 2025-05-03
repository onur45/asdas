<?php
require_once 'config/db.php';

try {
    // Önce mevcut admin kullanıcısını sil
    $sql = "DELETE FROM kullanicilar WHERE kullanici_adi = 'admin'";
    $db->exec($sql);

    // Yeni admin kullanıcısı oluştur
    $kullanici_adi = 'admin';
    $sifre = 'admin123';
    $ad_soyad = 'Sistem Yöneticisi';
    $email = 'admin@example.com';

    // Şifreyi hashle
    $hash = password_hash($sifre, PASSWORD_DEFAULT);

    $sql = "INSERT INTO kullanicilar (kullanici_adi, sifre, ad_soyad, email) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$kullanici_adi, $hash, $ad_soyad, $email]);

    echo "Admin kullanıcısı başarıyla oluşturuldu!<br>";
    echo "Kullanıcı adı: admin<br>";
    echo "Şifre: admin123";
} catch(PDOException $e) {
    echo "Hata oluştu: " . $e->getMessage();
}
?> 
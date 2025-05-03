<?php
require_once 'config/db.php';

// İzmir semtleri
$semtler = [
    'Alsancak', 'Buca', 'Bornova', 'Karşıyaka', 'Çiğli', 'Konak', 'Gaziemir', 'Bayraklı',
    'Narlıdere', 'Göztepe', 'Bostanlı', 'Hatay', 'Güzelbahçe', 'Urla', 'Menemen'
];

// Örnek firma isimleri
$firmalar = [
    'ABC Teknoloji Ltd. Şti.',
    'XYZ İnşaat A.Ş.',
    'Delta Lojistik',
    'Omega Danışmanlık',
    'Beta Yazılım',
    'Gamma Medikal',
    'Sigma Eğitim',
    'Theta Turizm',
    'Kappa Gıda',
    'Lambda Otomotiv'
];

// Örnek isimler
$isimler = [
    'Ahmet', 'Mehmet', 'Ayşe', 'Fatma', 'Ali', 'Veli', 'Zeynep', 'Elif',
    'Mustafa', 'Hüseyin', 'Emine', 'Hatice', 'Hasan', 'Hüseyin', 'Aysel',
    'Merve', 'Can', 'Cem', 'Deniz', 'Ebru', 'Fırat', 'Gül', 'Hakan',
    'İrem', 'Kemal', 'Leyla', 'Murat', 'Naz', 'Okan', 'Pınar', 'Rıza',
    'Selin', 'Tolga', 'Umut', 'Vedat', 'Yağmur', 'Zehra', 'Burak', 'Ceren',
    'Derya', 'Ege', 'Funda', 'Gizem', 'Hülya', 'İlker', 'Kaan', 'Lale',
    'Mert', 'Nur', 'Onur', 'Pelin'
];

// Örnek soyadlar
$soyadlar = [
    'Yılmaz', 'Kaya', 'Demir', 'Çelik', 'Şahin', 'Yıldız', 'Özdemir', 'Arslan',
    'Doğan', 'Kılıç', 'Aydın', 'Öztürk', 'Şen', 'Erdoğan', 'Aktaş', 'Özkan',
    'Kurt', 'Koç', 'Aslan', 'Çetin', 'Güneş', 'Yıldırım', 'Polat', 'Özcan',
    'Korkmaz', 'Çakır', 'Şeker', 'Yalçın', 'Güler', 'Özer', 'Kara', 'Özmen'
];

try {
    // Firmaları ekle
    echo "Firmalar ekleniyor...\n";
    $firma_sql = "INSERT INTO firmalar (firma_adi) VALUES (?)";
    $firma_stmt = $db->prepare($firma_sql);
    
    foreach ($firmalar as $firma) {
        $firma_stmt->execute([$firma]);
    }
    
    // Personelleri ekle
    echo "Personeller ekleniyor...\n";
    $personel_sql = "INSERT INTO personel (ad, soyad, telefon, semt, notlar) VALUES (?, ?, ?, ?, ?)";
    $personel_stmt = $db->prepare($personel_sql);
    
    for ($i = 0; $i < 50; $i++) {
        $ad = $isimler[array_rand($isimler)];
        $soyad = $soyadlar[array_rand($soyadlar)];
        $telefon = '05' . rand(10, 99) . rand(100, 999) . rand(10, 99) . rand(10, 99);
        $semt = $semtler[array_rand($semtler)];
        $notlar = rand(0, 1) ? 'Örnek not ' . rand(1, 100) : null;
        
        $personel_stmt->execute([$ad, $soyad, $telefon, $semt, $notlar]);
    }
    
    // İşleri ekle
    echo "İşler ekleniyor...\n";
    $is_sql = "INSERT INTO isler (personel_id, firma_id, tutar, firma_kari, miktar, birim, baslangic_tarihi, bitis_tarihi) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $is_stmt = $db->prepare($is_sql);
    
    $birimler = ['saat', 'gün', 'hafta', 'ay', 'yıl'];
    
    for ($i = 0; $i < 100; $i++) {
        $personel_id = rand(1, 50);
        $firma_id = rand(1, 10);
        $tutar = rand(100, 10000);
        $firma_kari = rand(50, 5000);
        $miktar = rand(1, 12);
        $birim = $birimler[array_rand($birimler)];
        
        // Rastgele tarih oluştur (son 1 yıl içinde)
        $baslangic = date('Y-m-d', strtotime('-' . rand(0, 365) . ' days'));
        $bitis = rand(0, 1) ? date('Y-m-d', strtotime($baslangic . ' +' . rand(1, 30) . ' days')) : null;
        
        $is_stmt->execute([$personel_id, $firma_id, $tutar, $firma_kari, $miktar, $birim, $baslangic, $bitis]);
    }
    
    echo "Test verileri başarıyla oluşturuldu!\n";
    
} catch(PDOException $e) {
    echo "Hata oluştu: " . $e->getMessage() . "\n";
}
?> 
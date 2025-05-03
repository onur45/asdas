CREATE DATABASE IF NOT EXISTS personel_takip CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE personel_takip;

CREATE TABLE IF NOT EXISTS personel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(50) NOT NULL,
    soyad VARCHAR(50) NOT NULL,
    telefon VARCHAR(20),
    semt VARCHAR(50),
    notlar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS firmalar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firma_adi VARCHAR(100) NOT NULL,
    notlar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS isler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personel_id INT NOT NULL,
    firma_id INT NOT NULL,
    tutar DECIMAL(10,2) NOT NULL,
    firma_kari DECIMAL(10,2) NOT NULL,
    miktar INT NOT NULL,
    birim ENUM('saat', 'gün', 'hafta', 'ay', 'yıl') NOT NULL,
    baslangic_tarihi DATE NOT NULL,
    bitis_tarihi DATE,
    notlar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personel_id) REFERENCES personel(id),
    FOREIGN KEY (firma_id) REFERENCES firmalar(id)
);

CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(50) UNIQUE NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    ad_soyad VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    son_giris DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO kullanicilar (kullanici_adi, sifre, ad_soyad, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sistem Yöneticisi', 'admin@example.com'); 
-- Önce personel tablosundaki foreign key'i kaldır
ALTER TABLE `personel` DROP FOREIGN KEY IF EXISTS `personel_ibfk_1`;
ALTER TABLE `personel` DROP FOREIGN KEY IF EXISTS `personel_ibfk_2`;
ALTER TABLE `personel` DROP FOREIGN KEY IF EXISTS `personel_ibfk_3`;

-- İlçeler tablosunu sil ve yeniden oluştur
DROP TABLE IF EXISTS `ilceler`;

CREATE TABLE `ilceler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ilce_adi` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- İlçeleri ekle
INSERT INTO `ilceler` (`ilce_adi`) VALUES
('Aliağa'),
('Balçova'),
('Bayındır'),
('Bayraklı'),
('Bergama'),
('Beydağ'),
('Bornova'),
('Buca'),
('Çeşme'),
('Çiğli'),
('Dikili'),
('Foça'),
('Gaziemir'),
('Güzelbahçe'),
('Karabağlar'),
('Karaburun'),
('Karşıyaka'),
('Kemalpaşa'),
('Kınık'),
('Kiraz'),
('Konak'),
('Menderes'),
('Menemen'),
('Narlıdere'),
('Ödemiş'),
('Seferihisar'),
('Selçuk'),
('Tire'),
('Torbalı'),
('Urla');

-- Personel tablosuna ilce_id sütununu ekle (eğer yoksa)
ALTER TABLE `personel` 
ADD COLUMN IF NOT EXISTS `ilce_id` int(11) DEFAULT NULL AFTER `telefon`;

-- Adres sütununu kaldır (eğer varsa)
ALTER TABLE `personel` DROP COLUMN IF EXISTS `adres`;

-- Foreign key kısıtlamasını ekle
ALTER TABLE `personel` 
ADD FOREIGN KEY (`ilce_id`) REFERENCES `ilceler`(`id`) ON DELETE SET NULL; 
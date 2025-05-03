ALTER TABLE `personel` 
ADD COLUMN `ilce_id` int(11) DEFAULT NULL AFTER `telefon`,
ADD COLUMN `adres` text DEFAULT NULL AFTER `ilce_id`,
ADD FOREIGN KEY (`ilce_id`) REFERENCES `ilceler`(`id`) ON DELETE SET NULL; 
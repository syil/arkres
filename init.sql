-- Arkres Veritabanı Başlangıç Scripti
-- Bu dosya konteynır ilk başlatıldığında çalışacaktır

-- Veritabanı karakter setini ayarla
ALTER DATABASE arkres_db CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Örnek tablo yapısı (Gerekli tabloları buraya ekleyin)
-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `seviye` tinyint(1) DEFAULT '0',
  `kayit_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kullanici_adi` (`kullanici_adi`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Galeriler tablosu
CREATE TABLE IF NOT EXISTS `galeriler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text,
  `ekleyen_id` int(11) DEFAULT NULL,
  `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  `seckin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ekleyen_id` (`ekleyen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Resimler tablosu
CREATE TABLE IF NOT EXISTS `resimler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `galeri_id` int(11) NOT NULL,
  `dosya_adi` varchar(255) NOT NULL,
  `genislik` int(11) DEFAULT NULL,
  `yukseklik` int(11) DEFAULT NULL,
  `yuklenme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `galeri_id` (`galeri_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Yorumlar tablosu
CREATE TABLE IF NOT EXISTS `yorumlar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resim_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `icerik` text NOT NULL,
  `tarih` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `resim_id` (`resim_id`),
  KEY `kullanici_id` (`kullanici_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Örnek veri ekle (isteğe bağlı)
-- INSERT INTO kullanicilar (kullanici_adi, email, sifre, seviye) 
-- VALUES ('admin', 'admin@arkres.com', MD5('admin123'), 3);

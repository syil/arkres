<?php
define("UZUN_TARIHSAAT", "%A, %d %B %Y %H:%M"); // Çarşamba, 22 Eylül 2010 02:39
define("KISA_TARIHSAAT", "%d.%m.%Y %H:%M");		// 22.10.2010 02:39
define("UZUN_TARIH", "%d %B %Y");				// 22 Eylül 2010
define("UZUN_TARIH2", "%d %B %A");				// Çarşamba, 22 Eylül
define("KISA_TARIH", "%d.%m.%Y");				// 22.10.2010
define("UZUN_SAAT", "%H:%M:%S");				// 02:39:27
define("KISA_SAAT", "%H:%M");					// 02:39
define("BOS_TARIH", "0000-00-00 00:00:00");		// 0000-00-00 00:00:00 (Boş Tarih)
define("MYSQL_TARIHSAAT", "%Y-%m-%d %H:%M:00");	// 2010-10-22 02:39:00 (MySQL Tarih-Zaman)
define("MYSQL_TARIH", "%Y-%m-%d");				// 2010-10-22 (MySQL Tarih)
// Özel tarih-saatler
define("GALERI_GUN", "%d");						// 22
define("GALERI_AY", "%b");						// Eyl
define("YORUM_TARIH", "%d %B, %Y saat %H:%M");	// 22 Eylül, 2010 saat 02:39
// İndirme referansları
define("IND_INDIRPHP", "indirphp");
define("IND_ARKRES", "arkres");

define("DEBUG", 1);
$head = array();

$Ayarlar = json_decode(file_get_contents('veri/ayarlar.json'));

$vt = new Veritabani($Ayarlar->Veritabani->Sunucu, $Ayarlar->Veritabani->KullaniciAdi, $Ayarlar->Veritabani->Sifre, $Ayarlar->Veritabani->VtAdi, $Ayarlar->Veritabani->KarakterSeti);

ResimIslemleri::VtAyarla($vt);
YorumIslemleri::VtAyarla($vt);
GaleriIslemleri::VtAyarla($vt);
KullaniciIslemleri::VtAyarla($vt);

KullaniciIslemleri::CerezdenKullanici(); // $Kullanici adında global değişken oluşturur

if (isset($_COOKIE[$Ayarlar->Kullanici->CozunurlukCerezi]))
	$coz = new Cozunurluk($_COOKIE[$Ayarlar->Kullanici->CozunurlukCerezi]);

$KullaniciSeviyeleri = array(
	3 => "Üst Yönetici", 
	2 => "Yönetici", 
	1 => "Editör", 
	0 => "Kullanıcı"
);
?>
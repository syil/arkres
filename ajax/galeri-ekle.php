<?php
chdir("..");
include "ic/sistem.php";

$title = post("title");
$content = post("content");
$tags = post("tags");
$uniqid = post("uniqid");
$ajax = post("ajax") or $ajax = false;
try {
	if (empty($title))
		throw new Exception(_r("Galeri başlığı belirtmelisin"));
	if (empty($tags))
		throw new Exception(_r("Etiket belirtmelisin"));
	if (empty($uniqid))
		throw new Exception(_r("Geçersiz işlem"));
	if (!giris())
		throw new Exception(_r("Giriş yapmış kullanıcı yok"));

	$yeni = new Galeri;
	$yeni->Baslik = $title;
	$yeni->Yazi = $content;
	$yeni->EkleyenID = $Kullanici->ID;
	$yeni->Onay = giris() ? 1 : 0;

	// Geçici klasördeki resimleri oku
	$gecici_klasor = $Ayarlar->Resim->GeciciKlasoru . $uniqid . "/";
	if (!file_exists($gecici_klasor))
		throw new Exception(_r("En az bir adet resim yüklemelisin"));
	if (dirsize($gecici_klasor) == 0)
		throw new Exception(_r("Resim yükleme işleminin bitmesini beklemelisin"));
	// Yeni Galeri veritabanına eklenir
	$yeni = GaleriIslemleri::Ekle($yeni);
	
	// Etiketler veritabanına eklenir
	$yeni->Islemler()->Etiketle(explode(",", $tags));
	
	$d = dir($gecici_klasor);
	$dosyaVar = false;
	while(false !== ($entry = $d->read())) {
		if ($entry == "." || $entry == "..")
			continue;
		$dosyaVar = true;
		$eski_konum = $gecici_klasor . $entry;
		
		$dosya_adi = pathinfo($eski_konum, PATHINFO_FILENAME). "-{$uniqid}.jpg";
		$yeni_konum = $Ayarlar->Resim->ResimKlasoru . $dosya_adi;
		
		// Dosyayı resimler klasörüne taşı
		rename($eski_konum, $yeni_konum);
		
		// Küçük resim ve önizleme oluştur
		kucuk_resim_olustur($yeni_konum, "{$Ayarlar->Resim->OnizlemeKlasoru}kucuk_{$dosya_adi}");
		onizleme_olustur($yeni_konum, "{$Ayarlar->Resim->OnizlemeKlasoru}onizleme_{$dosya_adi}");
		
		$resim = new Resim;
		$resim->Dosya = $dosya_adi;
		
		$yeni->Islemler()->ResimEkle($resim);
	}
	$d->close();
	
	if (!$dosyaVar) {
		throw new Exception(_r("En az bir adet resim yüklemelisin"));
	}

	if ($ajax) {
?>
	<p class="info">
		<strong>"<?php echo $yeni->Baslik; ?>" galerisi eklendi</strong>
		Eklediğiniz galerinin sayfasına yönlendiriliyorsunuz. Eğer yönlendirme başlamazsa <a href="<?php galeri_link($yeni); ?>">buraya</a> tıklayın.
	</p>
<?php
	}
}
catch (Exception $exc) {
	echo "<p class='error'><strong>". _r("Aşağıdaki hatayı düzeltmen gerekir"). "</strong><br />";
		echo "{$exc->getMessage()}<br />";
	echo "</p>";
}
$vt->Kapat();
?>
<?php
include "ic/sistem.php";

$resim_id = get("i");
$cozstr = get("c");
$goruntuleme = get("p") !== false;
$indir = isset($_GET["s"]) ? false : true;

try {
	$cozunurluk = new Cozunurluk($cozstr);
	$resim = ResimIslemleri::Al($resim_id);
	$direkt = "Evet";
	if ($goruntuleme) {
		$olay = "önizleme";
		$aciklama = "Resim Önizleme";
	}
	else {
		$olay = "indirme";
		$aciklama = "Resim indirme";
	}

	if (isset($_SERVER["HTTP_REFERER"]) && strpos($_SERVER["HTTP_REFERER"], $_SERVER["HTTP_HOST"]) !== false) {
		puanla("indirme", $resim->Galeri()->ID);
		$direkt = "Hayır";
	}
	
	gunlukle($olay, $aciklama, array(
		"Galeri" => $resim->Galeri()->Baslik,
		"Çözünürlük" => $cozstr, 
		"Kullanıcı" => giris() ? $Kullanici->Isim : $_SERVER["REMOTE_ADDR"],
		"Kullanıcı Çözünürlüğü" => isset($coz) ? $coz->__toString() : "-",
		"Direkt İndirme" => $direkt,
		"Resim ID" => $resim_id
	));
	
	resim_indir($resim_id, $cozunurluk, $indir);
}
catch (Exception $exc) {
	echo $exc->getMessage();
}
?>
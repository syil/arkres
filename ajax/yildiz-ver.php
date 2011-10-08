<?php
chdir("..");
include "ic/sistem.php";

$galeri_id = isset($_POST["g"]) ? $_POST["g"] : exit("-1");
$yildiz = 	 (isset($_POST["y"]) && $_POST["y"] > 0) ? $_POST["y"] : exit("-1");
$yildiz = $yildiz % ($Ayarlar->Diger->YildizSayisi + 1);

$galeri_isl = new GaleriIslemleri($galeri_id);
try {
	$galeri_isl->YildizVer($yildiz);
	puanla("yildiz", $galeri_id);
	yildizlari_yazdir($galeri_isl->Galeri()->Puan, $galeri_id);
}
catch(Exception $exc) {
	echo "-1";
}

$vt->Kapat();
?>